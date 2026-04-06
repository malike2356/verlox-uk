<div class="mx-auto max-w-6xl space-y-6">
    <div class="grid gap-6 md:grid-cols-2 md:items-start">
        <div class="space-y-6">
            @include('profile.partials.overview')
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="space-y-6">
            @include('profile.partials.sessions')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
