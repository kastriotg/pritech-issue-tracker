<?php

use App\Http\Requests\StoreCommentRequest;
use App\Models\Issue;
use Illuminate\Support\Facades\Validator;

it('authorizes comment store requests', function () {
    expect((new StoreCommentRequest)->authorize())->toBeTrue();
});

it('validates comment request data', function (callable $data, bool $passes) {
    $validator = Validator::make($data(), (new StoreCommentRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid comment' => [
        fn () => [
            'issue_id' => Issue::factory()->create()->id,
            'author_name' => 'Taylor Otwell',
            'body' => 'This needs another pass.',
        ],
        true,
    ],
    'missing issue' => [
        fn () => [
            'author_name' => 'Taylor Otwell',
            'body' => 'This needs another pass.',
        ],
        false,
    ],
    'missing author' => [
        fn () => [
            'issue_id' => Issue::factory()->create()->id,
            'body' => 'This needs another pass.',
        ],
        false,
    ],
    'missing body' => [
        fn () => [
            'issue_id' => Issue::factory()->create()->id,
            'author_name' => 'Taylor Otwell',
        ],
        false,
    ],
]);
