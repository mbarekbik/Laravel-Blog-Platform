<x-admin-layout>
    @section('scripts')
        @vite(['resources/js/filtr.js'])
    @endsection

    <x-dashboard-navbar route="{{ route('dashboard') }}"/>

    <div class="divided_minimal">
        <div class="comments">
            <div class="filter">
                <div class="filtr_collapse">
                    <p class="head">Komentarze</p>
                    <i class="fa-solid fa-caret-up button_collapse"></i>
                </div>
                <div class="filtr_body">
                    <div class="view">
                        <p class="name">Widok</p>
                        <div class="buttons view">
                            <div class="view_button list active" onclick="changeView('list', 'comment');"><i class="fa-solid fa-bars"></i></div>
                            <div class="view_button tiles" onclick="changeView('tile', 'comment');"><i class='bx bxs-grid-alt'></i></div>
                        </div>
                    </div>
                    <div class="sort">
                        <p class="name">Sortowanie</p>
                        <div class="buttons sort_buttons">
                            <div class="filter-button active" onclick="filterCheck(1);" data-order="desc">
                                <div class="dot"><i class="fa-solid fa-circle-check"></i></div>
                                <p>Nowe komentarze</p>
                            </div>
                            <div class="filter-button" onclick="filterCheck(2);" data-order="asc">
                                <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                                <p>Stare komentarze</p>
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
                    @role('Admin')
                        <div class="user">
                            <p class="name">Posty użytkownika</p>
                            <div class="buttons">
                                @foreach ($users as $user)
                                    @if (isset($selected_users) && in_array($user->toArray(), $selected_users))
                                        <div class="checkbox" onclick="selectUser(event, {{ $user->id }})" data-user-id="{{ $user->id }}">
                                            <div class="check"><i class="fa-solid fa-square-check"></i></div>
                                            <p>{{ $user->firstname . ' ' . $user->lastname }}</p>
                                        </div>
                                    @else
                                        <div class="checkbox" onclick="selectUser(event, {{ $user->id }})" data-user-id="{{ $user->id }}">
                                            <div class="check"><i class="fa-regular fa-square"></i></div>
                                            <p>{{ $user->firstname . ' ' . $user->lastname }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endrole
                    <div class="filter-button show_results">
                        <p>Zastosuj filtry</p>
                    </div>
                    <form style="display: none" id="filter_form">
                        <input type="text" id="term" name="q" value="{{ $term ?? '' }}">
                        <input type="text" id="order" name="order" value="{{ $order ?? 'desc' }}">
                        <input type="text" id="limit" name="limit" value="{{ $limit ?? 20 }}">
                        @role('Admin')
                            <input type="text" id="users" name="users[]" value="{{ is_array($selected_users_array) ? implode(',', $selected_users_array) : '' }}">
                        @endrole
                    </form>
                </div>
            </div>
            <div class="comments-list">
                @foreach ($comments as $comment)
                    <x-admin-comment-card :comment="$comment"/>
                @endforeach
            </div>
        </div>
{{--        @include('pagination.default', ['paginator' => $comments])--}}

        @if ((int)$limit !== 0)
            @can('post-super-list')
                {{ $comments->appends(['order' => $order ? $order : 'desc', 'limit' => $limit ? $limit : ($limit == 0 ? 0 : 20), 'users' => is_array($selected_users_array) ? $selected_users_array : ''])->links('pagination.default') }}
            @else
                {{ $comments->appends(['order' => $order ? $order : 'desc', 'limit' => $limit ? $limit : ($limit == 0 ? 0 : 20)])->links('pagination.default') }}
            @endcan
        @endif
    </div>
    <script type="module">
        const currentView = localStorage.getItem('commentView');
        const defaultView = currentView || 'list';
        if (defaultView === 'list') {
            changeView('list', 'comment');
        } else {
            changeView('tile', 'comment');
        }
    </script>
</x-admin-layout>
