<x-admin-layout>
    <x-dashboard-navbar route="{{ route('roles.index') }}"/>

    <div class="dashboard">
        <div class="role">
            <div class="welcome-2">Rola: {{ $role->name }}</div>
            <p class="role_label_2">Uprawnienia</p>
            <div class="role_list">
                @php
                    $last_label = '';
                @endphp
                @foreach ($rolePermissions as $permission)
                    @php
                        $label = explode('-', $permission->name);
                    @endphp
                    @if ($label[0] != $last_label)
                        @if($loop->index != 0)
                            </p>
                            </div>

                        @endif
                        <div class="role_container">
                        <p class="role_label">{{ $label[0] }}</p>
                        <p class="permissions">
                        @php
                            $last_label = $label[0];
                        @endphp
                    @endif

                    {{ $label[1] }}{{ isset($label[2]) ? '-' . $label[2] : '' }},

                @endforeach
            </div>
        </div>
        <a href="{{ route('roles.index') }}" class="role_exit">Powrót</a>
    </div>
</x-admin-layout>
