<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:image-list', ['only' => ['index']]);
        $this->middleware('permission:image-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        if ($request->input('q') !== null) {
            $terms = $request->input('q');
        } else {
            $terms = '';
        }

        if ($request->input('order') !== null) {
            $order = $request->input('order');
        } else {
            $order = 'descUsage';
        }

        if ($request->input('limit') !== null) {
            $limit = $request->input('limit');
        } else {
            $limit = 20;
        }

        if ($request->input('page') !== null) {
            $page = $request->input('page');
        } else {
            $page = 1;
        }

        if ($request->input('directories') !== null && $request->input('directories')[0] !== null) {
            if (is_array($request->input('directories'))) {
                $temp = explode(',', $request->input('directories')[0]);
            } else {
                $temp = explode(',', $request->input('directories'));
            }
            $selected_directories_array = $temp;
            $selected_directories = $request->input('directories');
            $directories = $selected_directories_array;
        } else {
            $selected_directories = null;
            $selected_directories_array = null;
            $directories = ['avatars', 'posts'];
        }

        [$fileList, $extensions, $duplicateNames, $uniqueNames] = $this->getFiles($directories);

        $publicPath = public_path('images');
        $directories = array_diff(scandir($publicPath), ['.', '..']);

        $filesCountByDirectory = [];

        foreach ($directories as $directory) {
            $path = $publicPath . DIRECTORY_SEPARATOR . $directory;

            if (is_dir($path)) {
                $files = array_diff(scandir($path), ['.', '..']);

                $filesCountByDirectory[$directory] = count($files);
            }
        }

        if ($terms !== null && $terms !== '') {
            $keywords = explode(' ', $terms);

            $fileList = array_filter($fileList, function($file) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (stripos($file['name'], $keyword) !== false || stripos($file['uniqid'], $keyword) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }

        if ($request->input('extensions') !== null && $request->input('extensions')[0] !== null) {
            if (is_array($request->input('extensions'))) {
                $temp = explode(',', $request->input('extensions')[0]);
            } else {
                $temp = explode(',', $request->input('extensions'));
            }
            $fileList = array_filter($fileList, function($file) use ($temp) {
                if (in_array($file['extension'], $temp)) {
                    return true;
                }
                return false;
            });
            $selected_extensions_array = $temp;
            $selected_extensions = $request->input('extensions');
        } else {
            $selected_extensions = null;
            $selected_extensions_array = null;
        }

        if ($request->input('duplicates') !== null && $request->input('duplicates')[0] !== null) {
            try {
                if (is_array($request->input('duplicates'))) {
                    $duplicates = explode(',', $request->input('duplicates')[0]);
                } else {
                    $duplicates = explode(',', $request->input('duplicates'));
                }
            } catch (\Exception $e) {
            }
            if ($duplicates[0] && $duplicates[1]) {
            } else {
                if ($duplicates[0]) {
                    $fileList = array_filter($fileList, function($file) use ($duplicateNames) {
                        if (in_array($file['fullname'], $duplicateNames)) {
                            return true;
                        }
                        return false;
                    });
                }
                if ($duplicates[1]) {
                    $fileList = array_filter($fileList, function($file) use ($uniqueNames) {
                        if (in_array($file['fullname'], $uniqueNames)) {
                            return true;
                        }
                        return false;
                    });
                }
            }
        } else {
            $duplicates = null;
        }

        switch ($order) {
            case 'asc':
                usort($fileList, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
                break;
            case 'desc':
                usort($fileList, function($a, $b) {
                    return strcmp($b['name'], $a['name']);
                });
                break;
            case 'ascAlphabetical':
                usort($fileList, function($a, $b) {
                    return strcmp($a['uniqid'], $b['uniqid']);
                });
                break;
            case 'descAlphabetical':
                usort($fileList, function($a, $b) {
                    return strcmp($b['uniqid'], $a['uniqid']);
                });
                break;
            case 'ascSize':
                usort($fileList, function($a, $b) {
                    return $a['size'] - $b['size'];
                });
                break;
            case 'descSize':
                usort($fileList, function($a, $b) {
                    return $b['size'] - $a['size'];
                });
                break;
            case 'ascUsage':
                usort($fileList, function($a, $b) {
                    return $a['usage_count'] - $b['usage_count'];
                });
                break;
            case 'descUsage':
                usort($fileList, function($a, $b) {
                    return $b['usage_count'] - $a['usage_count'];
                });
                break;
            default:
                usort($fileList, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
                break;
        }

        $basePath = URL::to('/');

        if ((int)$limit === 0) {
            $filesPaginated = $fileList;
        } else {
            $filesPaginated = $this->paginate($fileList, $limit, $page, ["path" => $basePath . "/dashboard/images", "pageName" => "page"]);
        }

        return view('image.index', [
            'files' => $filesPaginated,
            'directories' => $filesCountByDirectory,
            'extensions' => $extensions,
            'order' => $order,
            'limit' => $limit,
            'terms' => $terms,
            'page' => $page,
            'countDuplicates' => count($duplicateNames),
            'countUnique' => count($uniqueNames),
            'duplicates' => $duplicates,
            'selected_directories_array' => $selected_directories_array,
            'selected_directories' => $selected_directories,
            'selected_extensions_array' => $selected_extensions_array,
            'selected_extensions' => $selected_extensions,
        ]);
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: Paginator::resolveCurrentPage() ?: 1;

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function get(Request $request) {
        $offset = $request->input('offset', 0);
        $directoryPath = public_path("images\posts");
        $files = File::allFiles($directoryPath);
        $fileList = [];

        $files = array_slice($files, $offset, 20);

        foreach ($files as $file) {
            $fileName = $file->getFilename();

            $fileList[] = [
                'path' => "/images/posts/$fileName",
            ];
        }

        return response()->json($fileList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if (! Auth::User()) {
            abort(404);
        }

        return response()->json([
            'url' => $this->storeImage($request),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param string $name
     * @return JsonResponse
     */
    public function show(string $directory, string $name)
    {
        $basePath = public_path("images\\$directory");

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $fileName = pathinfo($name, PATHINFO_FILENAME);

        $filePath = $basePath . DIRECTORY_SEPARATOR . $name;

        if (File::exists($filePath)) {
            $fileSize = File::size($filePath);

            return response()->json([
                'name' => $fileName,
                'extension' => $extension,
                'directory' => $directory,
                'size' => $fileSize,
                'path' => "/images/$directory/$name",
                'used' => $this->searchImageUsage($directory, $fileName . "." . $extension)
            ]);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $name
     * @return RedirectResponse
     */
    public function destroy(string $directory, string $name)
    {
        $basePath = public_path("images\\$directory");

        $filePath = $basePath . DIRECTORY_SEPARATOR . $name;

        if (File::exists($filePath)) {
            try {
                File::delete($filePath);
                return redirect()->route('images.index');
            } catch (\Exception $e) {
                abort(500);
            }
        } else {
            abort(404);
        }
    }

    private function getFiles(array $directories) {
        $fileList = [];
        $extensions = [];
        $duplicateNames = [];
        $uniqueNames = [];

        foreach ($directories as $directory) {
            $directoryPath = public_path("images/$directory");
            $files = File::allFiles($directoryPath);

            $fileNameCounts = $this->getUniqueFileNames($files);

            $imageUsageCounts = $this->getImageUsageCounts($directories);

            foreach ($files as $file) {
                $filePath = $file->getPathname();
                $fileName = $file->getFilename();
                $fileSize = File::size($filePath);

                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                if (!empty($extension)) {
                    $extensions[$extension] = ($extensions[$extension] ?? 0) + 1;
                }

                $fileSizeFormatted = $fileSize;

                $fileNameWithoutUniqid = preg_replace('/^([a-z0-9]{13})-/', '', $fileName);

                preg_match('/^([a-z0-9]{13})/', $fileName, $matches);
                $uniqid = $matches[0] ?? null;

                $fileList[] = [
                    'fullname' => $fileName,
                    'path' => "/images/$directory/$fileName",
                    'directory' => $directory,
                    'name' => $fileNameWithoutUniqid,
                    'extension' => $extension,
                    'uniqid' => $uniqid,
                    'size' => $fileSizeFormatted,
                    'usage_count' => $imageUsageCounts["/images/$directory/$fileName"] ?? 0,
                ];
            }

            foreach ($fileNameCounts as $fileName => $fileNameWithoutUniqid) {
                $count = array_count_values($fileNameCounts)[$fileNameWithoutUniqid];
                if ($count > 1) {
                    $duplicateNames[] = $fileName;
                } else {
                    $uniqueNames[] = $fileName;
                }
            }
        }

        return [$fileList, $extensions, $duplicateNames, $uniqueNames];
    }

    private function getUniqueFileNames($files) {
        $fileNameCounts = [];
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $fileNameWithoutUniqid = preg_replace('/^([a-z0-9]{13})-/', '', $fileName);
            $fileNameCounts[$fileName] = $fileNameWithoutUniqid;
        }
        return $fileNameCounts;
    }

    private function getImageUsageCounts($directories) {
        $imageUsageCounts = [];
        foreach ($directories as $directory) {
            if ($directory === "posts"){
                $postTypes = ['posts', 'saved_posts', 'history_posts'];

                foreach ($postTypes as $postType) {
                    $contents = \DB::table($postType)->pluck('body');
                    $imagePaths = \DB::table($postType)->pluck('image_path');

                    foreach ($contents as $content) {
                        preg_match_all('/<img[^>]+src="([^"]+)"/', $content, $matches);

                        foreach ($matches[1] as $imagePath) {
                            $imagePath = parse_url($imagePath, PHP_URL_PATH);
                            $imageUsageCounts[$imagePath] = isset($imageUsageCounts[$imagePath]) ? $imageUsageCounts[$imagePath] + 1 : 1;
                        }
                    }

                    foreach ($imagePaths as $imagePath) {
                        $imageUsageCounts[$imagePath] = isset($imageUsageCounts[$imagePath]) ? $imageUsageCounts[$imagePath] + 1 : 1;
                    }
                }
            } elseif ($directory === "avatars") {
                $imagePaths = \DB::table('users')->pluck('image_path');

                foreach ($imagePaths as $imagePath) {
                    $imageUsageCounts[$imagePath] = isset($imageUsageCounts[$imagePath]) ? $imageUsageCounts[$imagePath] + 1 : 1;
                }
            }
        }
        return $imageUsageCounts;
    }

    private function searchImageUsage(string $directory, string $imageName)
    {
        $imageUsageInfo = [];
        $imageName = "/images/$directory/$imageName";

        if ($directory === "avatars") {
            $users = \DB::table('users')->get(['id', 'firstname', 'lastname', 'image_path']);
            foreach ($users as $user) {
                $imagePath = $user->image_path;
                if (str_contains($imagePath, $imageName)) {
                    $imageUsageInfo[] = [
                        'type' => 'Użytkownik',
                        'id' => $user->id,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'thumbnail' => $user->image_path,
                        'location' => 'Awatar'
                    ];
                }
            }
            return $imageUsageInfo;
        }

        $postTypes = [
            'posts' => 'Post',
            'saved_posts' => 'Zapisany post',
            'history_posts' => 'Historia'
        ];

        foreach ($postTypes as $table => $type) {
            $posts = \DB::table($table)->get(['id', 'title', 'body', 'image_path']);

            foreach ($posts as $post) {
                $thumbnailPath = $post->image_path;
                if (stripos($thumbnailPath, $imageName) !== false) {
                    $imageUsageInfo[] = [
                        'type' => $type,
                        'id' => $post->id,
                        'title' => $post->title,
                        'thumbnail' => $post->image_path,
                        'location' => 'Miniaturka'
                    ];
                }

                if (preg_match_all('/<img[^>]+src="([^"]+)"/', $post->body, $matches)) {
                    foreach ($matches[1] as $match) {
                        if (stripos($match, $imageName) !== false) {
                            $imageUsageInfo[] = [
                                'type' => $type,
                                'id' => $post->id,
                                'title' => $post->title,
                                'thumbnail' => $post->image_path,
                                'location' => 'Ciało'
                            ];
                        }
                    }
                }
            }
        }

        return $imageUsageInfo;
    }

    private function storeImage(Request $request)
    {
        $imageName = str_replace(' ', '-', $request->image->getClientOriginalName());
        $newImageName = uniqid().'-'.$imageName;
        $request->image->move(public_path('images\posts'), $newImageName);

        return '/images/posts/'.$newImageName;
    }

}
