<?php

use App\Foundation\Models\Tenant;
use App\Modules\Settings\Models\TenantSetting;
use Illuminate\Http\UploadedFile;

beforeEach(fn () => Storage::fake('public'));

test('upload lands in the correct tenant folder with name other than original file uploaded', function () {

    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $path = TenantSetting::firstOrFail()->logo_path;

    expect($path)->toStartWith("tenants/{$tenant->id}/")
        ->and(basename($path))->not()->toBe('logo.png')
        ->and($path)->toEndWith('.png');

    Storage::disk('public')->assertExists($path);
});

test('second upload deletes the first file', function () {
    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $firstPath = TenantSetting::firstOrFail()->logo_path;

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('new-logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $secondPath = TenantSetting::firstOrFail()->fresh()->logo_path;

    expect($secondPath)->not()->toBe($firstPath);

    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists($secondPath);
});

test('saving other fields with no file leaves the logo alone', function () {
    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $firstPath = TenantSetting::firstOrFail()->logo_path;

    $this->put(route('settings.business.update'), validSettingsPayload([
        'business_name' => 'New Business Name',
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $secondPath = TenantSetting::firstOrFail()->fresh()->logo_path;

    expect($secondPath)->toBe($firstPath);

    Storage::disk('public')->assertExists($firstPath);
});

test('removing the logo deletes the file', function () {
    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $firstPath = TenantSetting::firstOrFail()->logo_path;

    $this->put(route('settings.business.update'), validSettingsPayload([
        'remove_logo' => true,
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $secondPath = TenantSetting::firstOrFail()->fresh()->logo_path;

    expect($secondPath)->toBeNull();

    Storage::disk('public')->assertMissing($firstPath);
});

test('oversize file rejected, nothing written to disk', function () {
    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('logo.png')->size(6000),
    ]))
        ->assertRedirect()
        ->assertSessionHasErrors('logo');
    $logoPath = TenantSetting::firstOrFail()->logo_path;

    expect(TenantSetting::firstOrFail()->logo_path)->toBeNull();

    Storage::disk('public')->assertMissing($logoPath);
});

test('invalid file type rejected, nothing written to disk', function () {
    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->create('logo.pdf', 100, 'application/pdf'),
    ]))
        ->assertRedirect()
        ->assertSessionHasErrors('logo');
    $logoPath = TenantSetting::firstOrFail()->logo_path;

    expect(TenantSetting::firstOrFail()->logo_path)->toBeNull();

    Storage::disk('public')->assertMissing($logoPath);
});

test('extension comes from content, not filename', function () {
    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $pngFile = UploadedFile::fake()->image('logo.png', 100, 100);
    $disguisedFile = new UploadedFile($pngFile->getPathname(), 'logo.html', 'image/png', null, true);

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => $disguisedFile,
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $path = TenantSetting::firstOrFail()->logo_path;

    expect($path)->toEndWith('.png')
        ->not()->toEndWith('.html');

    Storage::disk('public')->assertExists($path);
});

test('failed save does not delete the old file', function () {
    Tenant::factory()->create();
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $firstPath = TenantSetting::firstOrFail()->logo_path;
    TenantSetting::updating(fn () => throw new RuntimeException('boom'));

    expect(fn () => $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('new-logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors()
    )->toThrow(RuntimeException::class);

    $secondPath = TenantSetting::firstOrFail()->fresh()->logo_path;

    expect($secondPath)->toBe($firstPath);

    Storage::disk('public')->assertExists($firstPath);
});
