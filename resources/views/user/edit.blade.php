<x-admin-layout>
    <x-dashboard-navbar route="{{ route('users.index') }}"/>

    <div class="dashboard">
        <form action="{{ route('users.update', $user->id) }}" method="POST" id="create_user">
            @csrf
            @method('PATCH')
            <div class="welcome-2">Edytuj użytkownika</div>
            <div class="body_form">
                @if(count($errors) > 0)
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <label>Imię</label>
                <input type="text" name="firstname" autocomplete="off" value="{{ $user->firstname }}">
                <label>Nazwisko</label>
                <input type="text" name="lastname" autocomplete="off" value="{{ $user->lastname }}">
                <label>Email</label>
                <input type="email" name="email" autocomplete="off" value="{{ $user->email }}">
                <label>Uprawnienia</label>
                <select name="roles">
                    @isset($roles)
                        @foreach ($roles as $role)
                            @if(isset($userRole[0]) && $userRole[0] == $role)
                                <option value="{{ $role }}" selected>{{ $role }}</option>
                            @else
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endif
                        @endforeach
                    @endisset
                </select>
                <label>Hasło</label>
                <div id="password_gen">
                    <input type="text" name="password" autocomplete="off">
                    <div class="button" onClick="generatePassword();">Generuj</div>
                </div>
                <label>Mail</label>
                <div class="mail">
                    <p>Wyślij email po edycji konta</p>
                    <label class="switch">
                        <input type="checkbox" name="send_mail" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <input type="submit" value="Edytuj">
            </div>
        </form>
    </div>
    <script>
        function generatePassword(){
            var pwdChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            var pwdLen = 10;
            var randPassword = new Array(pwdLen).fill(0).map(x => (function(chars) { let umax = Math.pow(2, 32), r = new Uint32Array(1), max = umax - (umax % chars.length); do { crypto.getRandomValues(r); } while(r[0] > max); return chars[r[0] % chars.length]; })(pwdChars)).join('');

            const passwordField = document.querySelector('input[name=password]');

            passwordField.value = randPassword;
        }
    </script>
</x-admin-layout>
