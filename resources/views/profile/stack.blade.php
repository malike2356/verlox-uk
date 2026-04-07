<div class="mx-auto max-w-7xl space-y-4">

    {{-- Profile hero --}}
    @include('profile.partials.overview')

    {{-- Two-column: edit form | security --}}
    <div class="grid gap-4 lg:grid-cols-5">
        <div class="min-w-0 lg:col-span-3">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="min-w-0 lg:col-span-2 space-y-4">
            @include('profile.partials.update-password-form')
            @include('profile.partials.sessions')
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
