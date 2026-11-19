<?php

namespace Tests\Feature;

use App\Models\GobogStock;
use App\Models\GobogTransaction;
use App\Models\Setting;
use App\Models\Tenan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemGobogNewTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'nama' => 'Admin Utama',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        Setting::set('harga_gobog', '5000');
        Setting::set('persen_admin', '20');
        Setting::set('persen_tenan', '80');

        GobogStock::create([
            'total_stok' => 12000,
            'stok_tersedia' => 12000,
            'stok_beredar' => 0,
        ]);
    }

    public function test_guest_redirected_to_login_and_admin_can_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_penjualan_mengurangi_stok_tersedia_dan_menolak_jika_melebihi_stok(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.penjualan.store'), ['jumlah' => 12001])
            ->assertRedirect(route('admin.penjualan.index'))
            ->assertSessionHas('error');

        $stock = GobogStock::current();
        $this->assertSame(12000, $stock->stok_tersedia);
        $this->assertSame(0, $stock->stok_beredar);

        $this->post(route('admin.penjualan.store'), ['jumlah' => 50])
            ->assertRedirect(route('admin.penjualan.index'))
            ->assertSessionHas('success');

        $stock->refresh();
        $this->assertSame(11950, $stock->stok_tersedia);
        $this->assertSame(50, $stock->stok_beredar);
        $this->assertSame(12000, $stock->total_stok);

        $this->assertDatabaseHas('gobog_transactions', [
            'tipe' => 'penjualan',
            'jumlah' => 50,
            'harga_satuan' => 5000,
            'total_nominal' => 250000,
        ]);
    }

    public function test_pengembalian_menambah_stok_tersedia_dan_menolak_jika_melebihi_beredar(): void
    {
        $this->actingAs($this->admin);
        $tenan = Tenan::factory()->create(['nama' => 'Warung Bu Sari']);

        $this->post(route('admin.penjualan.store'), ['jumlah' => 50]);

        $this->post(route('admin.pengembalian.store'), [
            'tenan_id' => $tenan->id,
            'jumlah' => 51,
        ])->assertSessionHas('error');

        $this->post(route('admin.pengembalian.store'), [
            'tenan_id' => $tenan->id,
            'jumlah' => 50,
        ])->assertSessionHas('success');

        $stock = GobogStock::current();
        $this->assertSame(12000, $stock->stok_tersedia);
        $this->assertSame(0, $stock->stok_beredar);

        $this->assertDatabaseHas('gobog_transactions', [
            'tipe' => 'pengembalian',
            'tenan_id' => $tenan->id,
            'jumlah' => 50,
            'harga_satuan' => 5000,
            'total_nominal' => 250000,
        ]);
    }

    public function test_tambah_dan_kurangi_stok_keseluruhan(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.stok.tambah'), [
            'jumlah' => 500,
            'keterangan' => 'Tambah stok baru',
        ])->assertSessionHas('success');

        $stock = GobogStock::current();
        $this->assertSame(12500, $stock->total_stok);
        $this->assertSame(12500, $stock->stok_tersedia);

        $this->post(route('admin.stok.kurangi'), [
            'jumlah' => 100,
            'keterangan' => 'Gobog rusak',
        ])->assertSessionHas('success');

        $stock->refresh();
        $this->assertSame(12400, $stock->total_stok);
        $this->assertSame(12400, $stock->stok_tersedia);
    }

    public function test_harga_transaksi_lama_tetap_tersimpan_saat_harga_global_berubah_dan_persentase_dinamis(): void
    {
        $this->actingAs($this->admin);
        $tenanA = Tenan::factory()->create(['nama' => 'Tenan A']);
        $tenanB = Tenan::factory()->create(['nama' => 'Tenan B']);

        $this->post(route('admin.penjualan.store'), ['jumlah' => 20]);
        $this->post(route('admin.pengembalian.store'), [
            'tenan_id' => $tenanA->id,
            'jumlah' => 10,
        ]);

        $this->post(route('admin.pengaturan.harga'), [
            'harga_gobog' => 7500,
            'keterangan' => 'Kenaikan harga',
        ])->assertSessionHas('success');

        $this->post(route('admin.pengembalian.store'), [
            'tenan_id' => $tenanB->id,
            'jumlah' => 10,
        ]);

        $txA = GobogTransaction::where('tenan_id', $tenanA->id)->first();
        $txB = GobogTransaction::where('tenan_id', $tenanB->id)->first();

        $this->assertEquals(5000, $txA->harga_satuan);
        $this->assertEquals(50000, $txA->total_nominal);
        $this->assertEquals(7500, $txB->harga_satuan);
        $this->assertEquals(75000, $txB->total_nominal);

        $response20 = $this->get(route('admin.laporan.index', ['tenan_id' => $tenanA->id]));
        $response20->assertOk();
        $response20->assertSee('Rp 50.000');
        $response20->assertSee('Rp 40.000');
        $response20->assertSee('Rp 10.000');

        $this->post(route('admin.pengaturan.bagi-hasil'), [
            'persen_admin' => 10,
            'persen_tenan' => 90,
        ])->assertSessionHas('success');

        $response10 = $this->get(route('admin.laporan.index', ['tenan_id' => $tenanA->id]));
        $response10->assertOk();
        $response10->assertSee('Rp 50.000');
        $response10->assertSee('Rp 45.000');
        $response10->assertSee('Rp 5.000');
    }

    public function test_pengaturan_bagi_hasil_menolak_jika_total_bukan_100_persen(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.pengaturan.bagi-hasil'), [
            'persen_admin' => 30,
            'persen_tenan' => 60,
        ])->assertSessionHasErrors('persen_admin');

        $this->assertEquals(20, Setting::persenAdmin());
        $this->assertEquals(80, Setting::persenTenan());
    }

    public function test_laporan_export_pdf_dan_proteksi_hapus_tenan_bertransaksi(): void
    {
        $this->actingAs($this->admin);
        $tenan = Tenan::factory()->create(['nama' => 'Tenan PDF']);

        $this->post(route('admin.penjualan.store'), ['jumlah' => 10]);
        $this->post(route('admin.pengembalian.store'), [
            'tenan_id' => $tenan->id,
            'jumlah' => 10,
        ]);

        $pdfResponse = $this->get(route('admin.laporan.export-pdf', [
            'filter' => 'mingguan',
            'tenan_id' => $tenan->id,
        ]));
        $pdfResponse->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $pdfResponse->headers->get('content-type'));

        $this->delete(route('admin.tenan.destroy', $tenan))
            ->assertRedirect(route('admin.tenan.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('tenans', ['id' => $tenan->id]);
    }
}
