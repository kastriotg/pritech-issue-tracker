<?php

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;

it('authorizes tag store requests', function () {
    expect((new StoreTagRequest)->authorize())->toBeTrue();
});

it('validates tag request data', function (callable $data, bool $passes) {
    $validator = Validator::make($data(), (new StoreTagRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid tag' => [
        fn () => ['name' => 'Bug', 'color' => '#EF4444'],
        true,
    ],
    'valid without color' => [
        fn () => ['name' => 'Feature'],
        true,
    ],
    'missing name' => [
        fn () => ['color' => '#EF4444'],
        false,
    ],
    'duplicate name' => [
        function () {
            Tag::factory()->create(['name' => 'Bug']);

            return ['name' => 'Bug', 'color' => '#EF4444'];
        },
        false,
    ],
    'invalid color' => [
        fn () => ['name' => 'Bug', 'color' => 'red'],
        false,
    ],
]);
