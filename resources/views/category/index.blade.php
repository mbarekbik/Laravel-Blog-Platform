<x-admin-layout>
    @section('scripts')
        @vite(['resources/js/filtr.js'])
    @endsection

    <x-dashboard-navbar route="{{ route('dashboard') }}"/>

    <div class="categories">
        <div class="filter">
            <div class="filtr_collapse">
                <p class="head">Kategorie</p>
                <i class="fa-solid fa-caret-up button_collapse"></i>
            </div>
            <div class="filtr_body">
                <div class="sort">
                    <p class="name">Sortowanie</p>
                    <div class="buttons sort_buttons">
                        <div class="filter-button" onclick="filterCheck(1);" data-order="desc">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>ID malejąco</p>
                        </div>
                        <div class="filter-button active" onclick="filterCheck(2);"  data-order="asc">
                            <div class="dot"><i class="fa-solid fa-circle-check"></i></div>
                            <p>ID rosnąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(3);" data-order="ascAlphabetical">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Alfabetycznie rosnąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(4);" data-order="descAlphabetical">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Alfabetycznie malejąco</p>
                        </div>
                    </div>
                </div>
                <div class="term">
                    <p class="name">Wyszukaj</p>
                    <div class="inputs">
                        <input type="text" name="term" value="{{ $terms ?? '' }}">
                    </div>
                </div>
                <div class="records">
                    <p class="name">Rekordy</p>
                    <div class="buttons">
                        <div class="filter-button rec_1" onclick="radioCheck(1);">
                            <span class="dot"><i class="fa-solid fa-square-xmark"></i></span>
                            <p>20 rekordów</p>
                        </div>
                        <div class="filter-button rec_2" onclick="radioCheck(2);">
                            <span class="dot"><i class="fa-regular fa-square"></i></span>
                            <p>50 rekordów</p>
                        </div>
                        <div class="filter-button rec_3" onclick="radioCheck(3);">
                            <span class="dot"><i class="fa-regular fa-square"></i></span>
                            <p>100 rekordów</p>
                        </div>
                        <div class="filter-button rec_4" onclick="radioCheck(4);">
                            <span class="dot"><i class="fa-regular fa-square"></i></span>
                            <p>Max rekordów</p>
                        </div>
                    </div>
                </div>
                <div class="filter-button show_results">
                    <p>Zastosuj filtry</p>
                </div>
                <form style="display: none" id="filter_form">
                    <input type="text" id="term" name="q" value="{{ $terms ?? '' }}">
                    <input type="text" id="order" name="order" value="{{ $order ?? 'desc' }}">
                    <input type="text" id="limit" name="limit" value="{{ $limit ?? 20 }}">
                </form>
            </div>
        </div>
        <div class="category_list">
            <table>
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nazwa</th>
                    <th scope="col">Tło</th>
                    <th scope="col">Kolor</th>
                    <th scope="col">Podgląd</th>
                    <th scope="col">Ilość postów</th>
                    <th scope="col">Akcje</th>
                </tr>
                </thead>
                <tbody class="body_user_list">
                @foreach ($categories as $category)
                    <tr>
                        <td data-label="ID">{{ $category->id }}</td>
                        <td data-label="Nazwa">{{ $category->name }}</td>
                        <td data-label="Tło">{{ $category->backgroundColor }} <span class="box" style="background: {{ $category->backgroundColor }};"> </span> </td>
                        <td data-label="Kolor">{{ $category->textColor }} <span class="box" style="background: {{ $category->textColor }};"> </span> </td>
                        <td data-label="Podgląd"><span class="preview" style="background: {{ $category->backgroundColor }}CC; color: {{ $category->textColor }};">{{ $category->name }} </span> </td>
                        <td data-label="Ilość postów">{{ $category->posts_count }} </td>
                        <td data-label="Akcje">
                            @can('category-edit')
                                <a href="{{ route('categories.edit', $category->id) }}" class="edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            @endcan
                            @can('category-delete')
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" id="category_{{ $category->id }}">
                                @method('DELETE')
                                @csrf
                                </form>
                                <button onClick="confirmDelete({{ $category->id }}, 'category')" class="delete"><i class="fa-solid fa-trash"></i></button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $categories->appends([
                 'q' => $terms ?? '',
                 'order' => $order ?? 'desc',
                 'limit' => $limit ?? 20,
            ])->links('pagination.default') }}
        </div>
    </div>
</x-admin-layout>
