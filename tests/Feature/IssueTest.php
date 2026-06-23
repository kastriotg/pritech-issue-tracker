<?php

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;

it('authorizes issue store and update requests', function () {
    expect((new StoreIssueRequest)->authorize())->toBeTrue()
        ->and((new UpdateIssueRequest)->authorize())->toBeTrue();
});

it('validates issue request data', function (callable $data, bool $passes) {
    $validator = Validator::make($data(), (new StoreIssueRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid issue' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'description' => 'Users should land on the dashboard.',
            'status' => 'open',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
            'tag_ids' => Tag::factory()->count(2)->create()->pluck('id')->all(),
        ],
        true,
    ],
    'valid without optional fields' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'in_progress',
            'priority' => 'medium',
        ],
        true,
    ],
    'missing project' => [
        fn () => [
            'title' => 'Fix login redirect',
            'status' => 'open',
            'priority' => 'high',
        ],
        false,
    ],
    'invalid status' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'blocked',
            'priority' => 'high',
        ],
        false,
    ],
    'invalid priority' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'open',
            'priority' => 'urgent',
        ],
        false,
    ],
    'invalid tag id' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'open',
            'priority' => 'high',
            'tag_ids' => [999],
        ],
        false,
    ],
]);

it('uses the same validation rules for issue updates', function () {
    expect((new UpdateIssueRequest)->rules())->toEqual((new StoreIssueRequest)->rules());
});
