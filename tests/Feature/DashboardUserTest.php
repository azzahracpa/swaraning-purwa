<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VisitSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_kosong_menampilkan_ajakan(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')
            ->assertOk()
            ->assertSee('Belum ada kunjungan terjadwal')
            ->assertSee('Belum ada koleksi tersimpan');
    }

    public function test_dashboard_menampilkan_kunjungan_terdekat_dan_ringkasan(): void
    {
        $user = User::factory()->create();
        $jauh = VisitSchedule::factory()->create(['visit_date' => today()->addDays(9)->toDateString()]);
        $dekat = VisitSchedule::factory()->create(['visit_date' => today()->addDays(2)->toDateString()]);
        $lewat = VisitSchedule::factory()->create(['visit_date' => today()->subDays(3)->toDateString()]);

        Reservation::factory()->for($user)->for($jauh, 'schedule')->create();
        $terdekat = Reservation::factory()->for($user)->for($dekat, 'schedule')->disetujui()->create();
        Reservation::factory()->for($user)->for($lewat, 'schedule')->disetujui()->create();
        // Reservasi milik orang lain tidak boleh ikut
        Reservation::factory()->for(VisitSchedule::factory()->create(['visit_date' => today()->addDay()->toDateString()]), 'schedule')->create();

        $user->favoriteCollections()->attach(Collection::factory()->create(['name' => 'Arca contoh dashboard']));

        $quiz = Quiz::factory()->aktif()->create(['title' => 'Kuis contoh dashboard']);
        Question::factory()->count(3)->for($quiz)->create(['points' => 10]);
        QuizAttempt::factory()->for($user)->for($quiz)->create(['score' => 20, 'completed_at' => now()]);
        Quiz::factory()->aktif()->create();

        $respons = $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->assertTrue($respons->viewData('kunjungan')->is($terdekat));
        $this->assertSame(3, (int) $respons->viewData('jumlahStatus')->sum());
        $this->assertSame(1, $respons->viewData('kuisBelumDikerjakan'));
        $respons->assertSee($terdekat->booking_code)
            ->assertSee('2 hari lagi')
            ->assertSee('Arca contoh dashboard')
            ->assertSee('Skor 20 dari 30 poin');
    }

    public function test_admin_yang_sudah_login_dibawa_ke_dashboard_admin_saat_membuka_login(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/login')
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs(User::factory()->create())->get('/register')
            ->assertRedirect(route('dashboard'));
    }
}
