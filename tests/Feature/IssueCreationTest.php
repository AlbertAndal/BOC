<?php

namespace Tests\Feature;

use App\Filament\Pages\BrokerMyIssues;
use App\Filament\Resources\Issues\IssueResource;
use App\Filament\Resources\Issues\Pages\CreateIssue;
use App\Models\Importer;
use App\Models\Issue;
use App\Models\Port;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class IssueCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    protected function createBrokerUser(): User
    {
        $role = Role::firstOrCreate(['role_name' => 'Customs Broker']);

        $user = User::firstOrCreate(
            ['username' => 'testbroker'],
            [
                'full_name' => 'Test Broker',
                'email' => 'broker@example.com',
                'password' => 'password',
                'role_id' => $role->role_id,
                'status' => 'Active',
            ]
        );

        $user->syncRoles(['Customs Broker']);

        return $user;
    }

    public function test_issue_create_page_displays_date_created_label(): void
    {
        $broker = $this->createBrokerUser();

        $response = $this->actingAs($broker)->get('/broker/issues/create');

        $response->assertSuccessful();
        $response->assertSee('Date Created');
        $response->assertDontSee('Date Received');
    }

    public function test_issue_stores_date_received_with_datetime(): void
    {
        $broker = $this->createBrokerUser();
        $importer = Importer::create(['importer_name' => 'ABC Logistics Inc.']);
        $port = Port::create(['port_name' => 'Port of Manila (POM)', 'location' => 'Manila']);

        $targetDateTime = '2026-09-16 14:35:20';

        $issue = Issue::create([
            'date_received' => $targetDateTime,
            'importer_id' => $importer->importer_id,
            'port_id' => $port->port_id,
            'concern' => 'Container inspection delay',
            'created_by' => $broker->user_id,
        ]);

        $this->assertInstanceOf(Carbon::class, $issue->date_received);
        $this->assertSame('2026-09-16 14:35:20', $issue->date_received->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('issues', [
            'issue_id' => $issue->issue_id,
            'date_received' => $targetDateTime,
        ]);
    }

    public function test_issue_defaults_date_received_to_current_datetime_when_omitted(): void
    {
        $broker = $this->createBrokerUser();
        $importer = Importer::create(['importer_name' => 'XYZ Global Trading']);
        $port = Port::create(['port_name' => 'MICP', 'location' => 'Manila']);

        Carbon::setTestNow('2026-09-16 10:15:30');

        $issue = Issue::create([
            'importer_id' => $importer->importer_id,
            'port_id' => $port->port_id,
            'concern' => 'Valuation inquiry',
            'created_by' => $broker->user_id,
        ]);

        $this->assertSame('2026-09-16 10:15:30', $issue->date_received->format('Y-m-d H:i:s'));

        Carbon::setTestNow();
    }

    public function test_broker_can_view_issue_on_my_issues_page(): void
    {
        $broker = $this->createBrokerUser();
        $importer = Importer::create(['importer_name' => 'ABC Logistics Inc.']);
        $port = Port::create(['port_name' => 'Port of Manila (POM)', 'location' => 'Manila']);

        $issue = Issue::create([
            'date_received' => now(),
            'importer_id' => $importer->importer_id,
            'port_id' => $port->port_id,
            'concern' => 'Sample broker concern for view page test',
            'created_by' => $broker->user_id,
        ]);

        $this->actingAs($broker);
        filament()->setCurrentPanel(filament()->getPanel('broker'));

        Livewire::test(BrokerMyIssues::class)
            ->assertTableActionVisible('view', $issue)
            ->assertTableActionExists('view', function ($action) use ($issue) {
                return str_contains($action->getUrl(), '/broker/issues/'.$issue->issue_id);
            }, $issue)
            ->assertSuccessful();
    }

    public function test_broker_edit_action_is_hidden_by_policy(): void
    {
        $broker = $this->createBrokerUser();
        $importer = Importer::create(['importer_name' => 'ABC Logistics Inc.']);
        $port = Port::create(['port_name' => 'Port of Manila (POM)', 'location' => 'Manila']);

        $issue = Issue::create([
            'date_received' => now(),
            'importer_id' => $importer->importer_id,
            'port_id' => $port->port_id,
            'concern' => 'Initial concern description',
            'created_by' => $broker->user_id,
        ]);

        $this->actingAs($broker);
        filament()->setCurrentPanel(filament()->getPanel('broker'));

        Livewire::test(BrokerMyIssues::class)
            ->assertTableActionHidden('edit', $issue)
            ->assertSuccessful();
    }

    public function test_broker_can_access_view_issue_page(): void
    {
        $broker = $this->createBrokerUser();
        $importer = Importer::create(['importer_name' => 'ABC Logistics Inc.']);
        $port = Port::create(['port_name' => 'Port of Manila (POM)', 'location' => 'Manila']);

        $issue = Issue::create([
            'date_received' => now(),
            'importer_id' => $importer->importer_id,
            'port_id' => $port->port_id,
            'concern' => 'Initial concern description',
            'created_by' => $broker->user_id,
        ]);

        $response = $this->actingAs($broker)->get("/broker/issues/{$issue->issue_id}");
        $response->assertSuccessful();
        $response->assertSee('Issue: '.$issue->issue_number);
        $response->assertSee('Back to Issues');

        filament()->setCurrentPanel(filament()->getPanel('broker'));
        $url = IssueResource::getUrl('view', ['record' => $issue]);
        $this->assertStringContainsString('/broker/issues/'.$issue->issue_id, $url);
    }

    public function test_existing_issue_attachments_are_accessible_in_public_storage(): void
    {
        Storage::disk('public')->put('issue-attachments/test.pdf', 'dummy content');
        $this->assertTrue(Storage::disk('public')->exists('issue-attachments/test.pdf'));
        Storage::disk('public')->delete('issue-attachments/test.pdf');
    }

    public function test_broker_submitting_issue_triggers_success_modal(): void
    {
        $broker = $this->createBrokerUser();
        $importer = Importer::create(['importer_name' => 'ABC Logistics Inc.']);
        $port = Port::create(['port_name' => 'Port of Manila (POM)', 'location' => 'Manila']);

        $this->actingAs($broker);
        filament()->setCurrentPanel(filament()->getPanel('broker'));

        Livewire::test(CreateIssue::class)
            ->fillForm([
                'importer_id' => $importer->importer_id,
                'port_id' => $port->port_id,
                'concern' => 'Detailed concern description for testing success modal',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertActionMounted('submissionSuccess');

        $this->assertDatabaseHas('issues', [
            'importer_id' => $importer->importer_id,
            'port_id' => $port->port_id,
            'concern' => 'Detailed concern description for testing success modal',
            'created_by' => $broker->user_id,
        ]);
    }
}
