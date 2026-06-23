<?php

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Validator;

it('authorizes project store and update requests', function () {
    expect((new StoreProjectRequest)->authorize())->toBeTrue()
        ->and((new UpdateProjectRequest)->authorize())->toBeTrue();
});

it('validates project request data', function (array $data, bool $passes) {
    $validator = Validator::make($data, (new StoreProjectRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid project' => [
        ['name' => 'Website Redesign', 'description' => 'Refresh the customer portal.'],
        true,
    ],
    'valid without description' => [
        ['name' => 'Website Redesign'],
        true,
    ],
    'missing name' => [
        ['description' => 'Refresh the customer portal.'],
        false,
    ],
    'name too long' => [
        ['name' => str_repeat('a', 256), 'description' => 'Refresh the customer portal.'],
        false,
    ],
    'description must be a string' => [
        ['name' => 'Website Redesign', 'description' => ['Refresh the customer portal.']],
        false,
    ],
]);

it('uses the same validation rules for project updates', function () {
    expect((new UpdateProjectRequest)->rules())->toBe((new StoreProjectRequest)->rules());
});
