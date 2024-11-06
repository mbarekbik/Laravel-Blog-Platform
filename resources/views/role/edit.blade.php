<x-admin-layout>
    <x-dashboard-navbar route="{{ route('roles.index') }}"/>

    <div class="dashboard">
        <div class="role">
            <div class="welcome-2">Rola: {{ $role->name }}</div>
            <p class="role_label_2">Uprawnienia</p>
            <form action="{{ route('roles.update', $role->id) }}" method="POST" class="role_list">
                @csrf
                @method('PATCH')
                @php
                    $last_label = '';
                @endphp
                @foreach ($permissions as $permission)
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

                    <label class="container">{{ $label[1] }}{{ isset($label[2]) ? '-' . $label[2] : '' }}
                    @if(in_array($permission->id, $rolePermissions))
                        <input type="checkbox" name="permission[]" value="{{ $permission->id }}" checked>
                    @else
                        <input type="checkbox" name="permission[]" value="{{ $permission->id }}">
                    @endif
                    <span class="checkmark"></span>
                    </label>

                @endforeach
                </div>
                <input type="hidden" name="name" value="{{ $role->name }}">
                <input type="submit" value="Edytuj">
            </form>
        </div>
    </div>
</x-admin-layout>
