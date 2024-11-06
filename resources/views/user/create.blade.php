<x-admin-layout>
    <x-dashboard-navbar route="{{ route('dashboard') }}"/>

    <div class="dashboard">
        <form action="{{ route('users.store') }}" method="POST" id="create_user">
            @csrf
            <div class="welcome-2">Dodaj użytkownika</div>
            @if(count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="body_form">
                <label>Imię</label>
                <input type="text" name="firstname" autocomplete="off">
                <label>Nazwisko</label>
                <input type="text" name="lastname" autocomplete="off">
                <label>Email</label>
                <input type="email" name="email" autocomplete="off">
                <label>Uprawnienia</label>
                <select name="roles">
                    @isset($roles)
                        @foreach ($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
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
                    <p>Wyślij email po założeniu konta</p>
                    <label class="switch">
                        <input type="checkbox" name="send_mail" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <input type="submit" value="Utwórz">
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
