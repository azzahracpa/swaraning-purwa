<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Quiz;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StrukturDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_empat_belas_tabel_erd_tersedia(): void
    {
        $tabel = [
            'users', 'museum_profiles', 'categories', 'collections', 'collection_images',
            'favorites', 'articles', 'visit_schedules', 'reservations', 'quizzes',
            'questions', 'answer_options', 'quiz_attempts', 'quiz_answers',
        ];

        foreach ($tabel as $nama) {
            $this->assertTrue(Schema::hasTable($nama), "Tabel {$nama} belum ada.");
        }

        // Tabel teknis tambahan untuk SESSION_DRIVER dan CACHE_STORE database
        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('cache'));
    }

    public function test_kolom_users_sesuai_erd(): void
    {
        $this->assertSame(
            ['id', 'name', 'email', 'password', 'phone', 'role', 'created_at', 'updated_at'],
            Schema::getColumnListing('users')
        );
    }

    public function test_seeder_berjalan_dan_relasi_terhubung(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->articles()->exists());

        $koleksi = Collection::with(['category', 'images', 'primaryImage'])->first();
        $this->assertNotNull($koleksi->category);
        $this->assertNotNull($koleksi->primaryImage);

        $quiz = Quiz::with('questions.options')->aktif()->first();
        foreach ($quiz->questions as $soal) {
            // Aturan: tepat satu opsi benar per soal
            $this->assertSame(1, $soal->options->where('is_correct', true)->count());
        }
    }

    public function test_favorit_tidak_boleh_duplikat_di_database(): void
    {
        $user = User::factory()->create();
        $koleksi = Collection::factory()->create();

        $user->favoriteCollections()->attach($koleksi->id);

        $this->expectException(QueryException::class);
        $user->favoriteCollections()->attach($koleksi->id);
    }

    public function test_sisa_kuota_hanya_dikurangi_reservasi_disetujui(): void
    {
        $jadwal = VisitSchedule::factory()->create(['capacity' => 20]);

        Reservation::factory()->for($jadwal, 'schedule')->disetujui()->create(['total_visitors' => 8]);
        Reservation::factory()->for($jadwal, 'schedule')->create(['total_visitors' => 5]);

        $this->assertSame(12, $jadwal->sisaKuota());
    }
}
