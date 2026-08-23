<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AboutController extends Controller
{
    public function index()
    {
        $about = config('about');

        $members = collect($about['members'] ?? [])->map(function (array $member) {
            $photo = $member['photo'] ?? null;
            $member['photo_url'] = ($photo && $this->photoExists($photo))
                ? route('about.photo', ['file' => $photo])
                : null;
            $member['initials'] = $this->initials($member['name'] ?? '?');

            return $member;
        })->all();

        return view('about.index', [
            'about' => $about,
            'members' => $members,
        ]);
    }

    /**
     * Stream a team photo from the private (non-public) folder.
     * Only filenames listed in config/about.php are allowed.
     */
    public function photo(string $file): BinaryFileResponse
    {
        $file = basename($file);
        if (! $this->isAllowedPhoto($file) || ! $this->photoExists($file)) {
            abort(404);
        }

        $path = $this->photoPath($file);
        $mime = File::mimeType($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function photoDirectory(): string
    {
        return storage_path('app/private/about/team');
    }

    private function photoPath(string $file): string
    {
        return $this->photoDirectory().DIRECTORY_SEPARATOR.$file;
    }

    private function photoExists(string $file): bool
    {
        $path = $this->photoPath(basename($file));

        return is_file($path);
    }

    private function isAllowedPhoto(string $file): bool
    {
        $allowed = collect(config('about.members', []))
            ->pluck('photo')
            ->filter()
            ->map(fn ($p) => basename((string) $p))
            ->all();

        return in_array($file, $allowed, true);
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
            if (mb_strlen($letters) >= 2) {
                break;
            }
        }

        return $letters !== '' ? $letters : '?';
    }
}
