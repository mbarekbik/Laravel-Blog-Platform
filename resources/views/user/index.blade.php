<x-admin-layout>
    @section('scripts')
        @vite(['resources/js/filtr.js'])
    @endsection

    <x-dashboard-navbar route="{{ route('dashboard') }}"/>

    <div class="users">
        <div class="filter">
            <div class="filtr_collapse">
                <p class="head">Użytkownicy</p>
                <i class="fa-solid fa-caret-up button_collapse"></i>
            </div>
            <div class="filtr_body">
                <div class="sort">
                    <p class="name">Sortowanie</p>
                    <div class="buttons sort_buttons">
                        <div class="filter-button active" onclick="filterCheck(1);" data-order="desc">
                            <div class="dot"><i class="fa-solid fa-circle-check"></i></div>
                            <p>Najnowsze</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(2);" data-order="asc">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Najstarsze</p>
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
                <div class="roles_filtr">
                    <p class="name">Rola</p>
                    <div class="buttons">
                        @foreach ($roles as $role)
                            @if (isset($selected_roles_array) && in_array($role->id, $selected_roles_array))
                                <div class="checkbox" onclick="selectRole(event, {{ $role->id }})" data-role-id="{{ $role->id }}">
                                    <div class="check"><i class="fa-solid fa-square-check"></i></div>
                                    <p>{{ $role->name }} ({{ $role->users_count }})</p>
                                </div>
                            @else
                                <div class="checkbox" onclick="selectRole(event, {{ $role->id }})" data-role-id="{{ $role->id }}">
                                    <div class="check"><i class="fa-regular fa-square"></i></div>
                                    <p>{{ $role->name }} ({{ $role->users_count }})</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="filter-button show_results">
                    <p>Zastosuj filtry</p>
                </div>
                <form style="display: none" id="filter_form">
                    <input type="text" id="term" name="q" value="{{ $terms ?? '' }}">
                    <input type="text" id="order" name="order" value="{{ $order ?? 'desc' }}">
                    <input type="text" id="limit" name="limit" value="{{ $limit ?? 20 }}">
                    <input type="text" id="roles" name="roles[]" value="{{ is_array($selected_roles_array) ? implode(',', $selected_roles_array) : '' }}">
                </form>
            </div>
        </div>
        <div class="users_list">
            <table>
                <thead>
                    <tr>
                        <th scope="col">IMG</th>
                        <th scope="col">Imię</th>
                        <th scope="col">Nazwisko</th>
                        <th scope="col">Email</th>
                        <th scope="col">Rola</th>
                        <th scope="col" style="width:125px;">Akcje</th>
                    </tr>
                </thead>
                <tbody class="body_user_list">
                    @foreach ($users as $key => $user)
                        <tr>
                            <td data-label="IMG"><img src="{{ asset($user->image_path) }}" alt="{{ $user->firstname }}"></td>
                            <td data-label="Imię">{{ $user->firstname }}</td>
                            <td data-label="Nazwisko">{{ $user->lastname }}</td>
                            <td data-label="Email">{{ $user->email }}</td>
                            <td data-label="Rola">
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $v)
                                        <span>{{ $v }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td data-label="Akcje">
                                @can('user-edit')
                                    <a href="{{ route('users.edit', $user->id) }}" class="edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                @endcan
                                @can('user-delete')
                                    @if(Auth::id() == $user->id)
                                        <button class="delete" onClick="cannot('Nie można usunąć swojego konta!')"><i class="fa-solid fa-trash"></i></a>
                                        @else
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" id="user_{{ $user->id }}">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        <button class="delete" onClick="confirmDelete({{ $user->id }}, 'user')"><i class="fa-solid fa-trash"></i></a>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->appends([
                 'q' => $terms ?? '',
                 'order' => $order ?? 'desc',
                 'limit' => $limit ?? 20,
                 'users' => is_array($selected_roles_array) ? $selected_roles_array : [],
            ])->links('pagination.default') }}
        </div>
    </div>
    <script>

    </script>
</x-admin-layout>
