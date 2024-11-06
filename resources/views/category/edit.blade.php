<x-admin-layout>
    <x-dashboard-navbar route="{{ route('categories.index') }}"/>

    <div class="dashboard">
        <form action="{{ route('categories.update', $category->id) }}" method="POST" id="edit_category">
            @csrf
            @method('PATCH')
            <div class="welcome-2">Edytuj kategorię</div>
            <div class="body_form">
                @if(count($errors) > 0)
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <label>Nazwa</label>
                <input type="text" name="name" autocomplete="off" value="{{ $category->name }}">
                <label>Tło</label>
                <input type="text" name="backgroundColor" autocomplete="off" value="{{ $category->backgroundColor }}">
                <label>Kolor</label>
                <input type="text" name="textColor" autocomplete="off" value="{{ $category->textColor }}">
                <input type="submit" value="Edytuj">
            </div>
        </form>
    </div>
</x-admin-layout>
