<x-admin-layout>
    @section('scripts')
        @vite(['resources/js/filtr.js'])
        @vite(['resources/js/image.js'])
    @endsection

    <x-dashboard-navbar route="{{ route('dashboard') }}"/>

    <div class="images">
        <div class="filter">
            <div class="filtr_collapse">
                <p class="head">Obrazy</p>
                <i class="fa-solid fa-caret-up button_collapse"></i>
            </div>
            <div class="filtr_body">
                <div class="sort">
                    <p class="name">Sortowanie</p>
                    <div class="buttons sort_buttons">
                        <div class="filter-button" onclick="filterCheck(1);" data-order="asc">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Alfabetycznie nazwa rosnąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(2);" data-order="desc">
                            <div class="dot"><i class="fa-solid fa-circle-check"></i></div>
                            <p>Alfabetycznie nazwa malejąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(3);" data-order="ascAlphabetical">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Alfabetycznie uniqid rosnąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(4);" data-order="descAlphabetical">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Alfabetycznie uniqid malejąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(5);" data-order="ascSize">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Rozmiar rosnąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(6);" data-order="descSize">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Rozmiar malejąco</p>
                        </div>
                        <div class="filter-button" onclick="filterCheck(7);" data-order="ascUsage">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Użycia rosnąco</p>
                        </div>
                        <div class="filter-button active" onclick="filterCheck(8);" data-order="descUsage">
                            <div class="dot"><i class="fa-solid fa-circle-dot"></i></div>
                            <p>Użycia malejąco</p>
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
                <div class="directories">
                    <p class="name">Folder</p>
                    <div class="buttons">
                        @foreach ($directories as $directory => $count)
                            @if (isset($selected_directories_array) && in_array($directory, $selected_directories_array))
                                <div class="checkbox" onclick="selectDirectory(event, '{{ $directory }}')" data-directory-name="{{ $directory }}">
                                    <div class="check"><i class="fa-solid fa-square-check"></i></div>
                                    <p>{{ $directory }} ({{ $count }})</p>
                                </div>
                            @else
                                <div class="checkbox" onclick="selectDirectory(event, '{{ $directory }}')" data-directory-name="{{ $directory }}">
                                    <div class="check"><i class="fa-regular fa-square"></i></div>
                                    <p>{{ $directory }} ({{ $count }})</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="extensions">
                    <p class="name">Rozszerzenie</p>
                    <div class="buttons">
                        @foreach ($extensions as $extension => $count)
                            @if (isset($selected_extensions_array) && in_array($extension, $selected_extensions_array))
                                <div class="checkbox" onclick="selectExtension(event, '{{ $extension }}')" data-extension-name="{{ $extension }}">
                                    <div class="check"><i class="fa-solid fa-square-check"></i></div>
                                    <p>{{ $extension }} ({{ $count }})</p>
                                </div>
                            @else
                                <div class="checkbox" onclick="selectExtension(event, '{{ $extension }}')" data-extension-name="{{ $extension }}">
                                    <div class="check"><i class="fa-regular fa-square"></i></div>
                                    <p>{{ $extension }} ({{ $count }})</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="duplicates">
                    <p class="name">Duplikaty</p>
                    <div class="buttons">
                        <div class="checkbox" onclick="selectDuplicates('yes');" data-duplicates="yes">
                            <div class="check"><i class="{{ isset($duplicates) ? ($duplicates[0] === '1' ? 'fa-solid fa-square-check' : 'fa-regular fa-square') : 'fa-regular fa-square' }}"></i></div>
                            <p>Tak ({{ $countDuplicates }})</p>
                        </div>
                        <div class="checkbox" onclick="selectDuplicates('no');" data-duplicates="no">
                            <div class="check"><i class="{{ isset($duplicates) ? ($duplicates[1] === '1' ? 'fa-solid fa-square-check' : 'fa-regular fa-square') : 'fa-regular fa-square' }}"></i></div>
                            <p>Nie ({{ $countUnique }})</p>
                        </div>
                    </div>
                </div>
                <div class="filter-button show_results">
                    <p>Zastosuj filtry</p>
                </div>
                <form style="display: none" id="filter_form">
                    <input type="text" id="term" name="q" value="{{ $terms ?? '' }}">
                    <input type="text" id="order" name="order" value="{{ $order ?? 'descUsage' }}">
                    <input type="text" id="limit" name="limit" value="{{ $limit ?? 20 }}">
                    <input type="text" id="directories" name="directories[]" value="{{ !empty($selected_directories[0]) ? $selected_directories[0] : null }}">
                    <input type="text" id="extensions" name="extensions[]" value="{{ !empty($selected_extensions[0]) ? $selected_extensions[0] : null }}">
                    <input type="text" id="duplicates" name="duplicates[]" value="{{ $duplicates ? implode(',', $duplicates) : '' }}">
                </form>
            </div>
        </div>
        <div class="image_list">
            @php
                function formatBytes($bytes, $precision = 2) {
                    $kilobyte = 1024;
                    $megabyte = $kilobyte * 1024;

                    if ($bytes < $kilobyte) {
                        return $bytes . ' <span>B</span>';
                    } elseif ($bytes < $megabyte) {
                        return number_format($bytes / $kilobyte, $precision) . ' <span>KB</span>';
                    } else {
                        return number_format($bytes / $megabyte, $precision) . ' <span>MB</span>';
                    }
                }
            @endphp
            @foreach($files as $file)
                <x-image-card :image="$file"/>
            @endforeach

            @if ((int)$limit !== 0)
                {{ $files->appends([
                     'q' => $terms ?? '',
                     'order' => $order ?? 'descUsage',
                     'limit' => $limit ?? 20,
                     'directories' => !empty($selected_directories) ? $selected_directories : null,
                     'extensions' => !empty($selected_extensions) ? $selected_extensions : null,
                     'duplicates' => $duplicates ? implode(',', $duplicates) : ''
                ])->links('pagination.default') }}
            @endif
        </div>
        <img src="" class="background" alt="" style="display: none;">
        <div class="image_modal" style="display: none;">
            <img src="" class="thumbnail" alt="">
            <div class="close" onclick="closeModal();"><i class="fa-solid fa-xmark"></i></div>
            <div class="file_info">
                <p class="directory"><i class="fa-solid fa-folder"></i> na</p>
                <div class="filename">na</div>
                <div class="size"><i class="fa-solid fa-database"></i> 0</div>
                <div class="usage_count"><i class="fa-solid fa-recycle"></i> Użyto: <span>0</span></div>
                @can('image-delete')
                    <button class="button" onclick="deleteImage(event);" data-name="na">Usuń <i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                @endcan
                <div class="use_info">Użycia:</div>
                <div class="used">
                    <div class="post">
                        <img src="" alt="">
                        <div class="info">
                            <div class="type">Post</div>
                            <div class="location">Ciało</div>
                            <div class="title">Tytuł</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
