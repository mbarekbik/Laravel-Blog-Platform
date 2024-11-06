<x-admin-layout>
    <x-dashboard-navbar route="{{ route('dashboard') }}"/>

    <div class="dashboard">
        <form action="{{ route('roles.store') }}" method="POST" id="create_role">
            <div class="welcome-2">Dodaj role</div>
            @if(count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="body_form">
                @csrf
                <label>Nazwa</label>
                <input type="text" name="name" autocomplete="off">
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
                    <input type="checkbox" name="permission[]" value="{{ $permission->id }}">
                    <span class="checkmark"></span>
                    </label>
                @endforeach
                </div>
                <input type="submit" value="Utwórz">
            </div>
        </form>
    </div>
</x-admin-layout>
