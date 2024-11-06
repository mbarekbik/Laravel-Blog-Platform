<x-admin-layout>
    <x-dashboard-navbar route="{{ route('dashboard') }}"/>

    <div class="dashboard">
        <form action="{{ route('categories.store') }}" method="POST" id="create_category">
            @csrf
            <div class="welcome-2">Dodaj kategorię</div>
            @if(count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="body_form">
                <label>Nazwa</label>
                <input type="text" name="name" autocomplete="off">
                <label>Tło</label>
                <input type="text" name="backgroundColor" autocomplete="off">
                <label>Kolor</label>
                <input type="text" name="textColor" autocomplete="off">
                <input type="submit" value="Utwórz">
            </div>
        </form>
    </div>
</x-admin-layout>
