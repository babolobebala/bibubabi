<?php

namespace Modules\Tool\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use Throwable;
use ZipArchive;

class TemplateDocumentGeneratorService
{
    private const WORD_NAMESPACE = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    private const PACKAGE_RELATIONSHIP_NAMESPACE = 'http://schemas.openxmlformats.org/package/2006/relationships';

    private const CONTENT_TYPE_NAMESPACE = 'http://schemas.openxmlformats.org/package/2006/content-types';

    private const WORD_DRAWING_NAMESPACE = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';

    private const DRAWING_NAMESPACE = 'http://schemas.openxmlformats.org/drawingml/2006/main';

    private const PICTURE_NAMESPACE = 'http://schemas.openxmlformats.org/drawingml/2006/picture';

    private const RELATIONSHIP_NAMESPACE = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    public function generate(array $data, ?UploadedFile $fotoSatu = null, ?UploadedFile $fotoDua = null): string
    {
        $templatePath = base_path('Modules/Tool/resources/assets/kosongan.docx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('Template dokumen tidak ditemukan.');
        }

        $normalizedTemplatePath = $this->createTemporaryDocxPath();
        $generatedPath = $this->createTemporaryDocxPath();
        $temporaryImagePaths = [];
        $debug = [
            'has_foto_satu' => (bool) ($data['has_foto_satu'] ?? false),
            'has_foto_dua' => (bool) ($data['has_foto_dua'] ?? false),
            'foto_satu_request' => $this->describeUploadedFile($fotoSatu),
            'foto_dua_request' => $this->describeUploadedFile($fotoDua),
        ];

        $this->assertExpectedImagesArePresent($data, $fotoSatu, $fotoDua);

        try {
            if (! copy($templatePath, $normalizedTemplatePath)) {
                throw new RuntimeException('Gagal menyalin template dokumen.');
            }

            $this->normalizeTemplatePlaceholders($normalizedTemplatePath, [
                'nama_petugas',
                'anggaran_membiayai',
                'tujuan',
                'anggaran_diperiksa',
                'jadwal',
                'ringkasan_hasil',
                'pejabat_dikunjungi',
                'dokumentasi_foto1',
                'dokumentasi_foto2',
            ]);

            $templateProcessor = new TemplateProcessor($normalizedTemplatePath);
            $templateProcessor->setValues($this->textPlaceholders($data));
            $templateProcessor->saveAs($generatedPath);

            $imageMap = [
                'dokumentasi_foto1' => $fotoSatu instanceof UploadedFile
                    ? $this->prepareUploadedImage($fotoSatu, $temporaryImagePaths)
                    : null,
                'dokumentasi_foto2' => $fotoDua instanceof UploadedFile
                    ? $this->prepareUploadedImage($fotoDua, $temporaryImagePaths)
                    : null,
            ];
            $debug['image_map'] = $imageMap;

            $this->injectDocumentationImages($generatedPath, $imageMap, $debug);
            $this->writeDebugSnapshot($debug);

            return $generatedPath;
        } catch (Throwable $throwable) {
            $debug['exception'] = $throwable->getMessage();
            $this->writeDebugSnapshot($debug);

            if (is_file($generatedPath)) {
                @unlink($generatedPath);
            }

            throw new RuntimeException('Gagal membuat dokumen Word: '.$throwable->getMessage(), previous: $throwable);
        } finally {
            foreach ($temporaryImagePaths as $temporaryImagePath) {
                if (is_string($temporaryImagePath) && is_file($temporaryImagePath)) {
                    @unlink($temporaryImagePath);
                }
            }

            if (is_file($normalizedTemplatePath)) {
                @unlink($normalizedTemplatePath);
            }
        }
    }

    public function buildDownloadFilename(array $data): string
    {
        $name = preg_replace('/[^A-Za-z0-9]+/', '-', strtolower(trim((string) ($data['nama_petugas'] ?? 'hasil'))));
        $name = trim((string) $name, '-');

        return 'laporan-perjalanan-dinas-'.($name !== '' ? $name : 'hasil').'.docx';
    }

    private function textPlaceholders(array $data): array
    {
        return [
            'nama_petugas' => (string) $data['nama_petugas'],
            'anggaran_membiayai' => (string) $data['anggaran_membiayai'],
            'tujuan' => (string) $data['tujuan'],
            'anggaran_diperiksa' => (string) $data['anggaran_diperiksa'],
            'jadwal' => (string) $data['jadwal'],
            'ringkasan_hasil' => (string) $data['ringkasan_hasil'],
            'pejabat_dikunjungi' => (string) $data['pejabat_dikunjungi'],
        ];
    }

    private function assertExpectedImagesArePresent(array $data, ?UploadedFile $fotoSatu, ?UploadedFile $fotoDua): void
    {
        if (($data['has_foto_satu'] ?? false) && ! $fotoSatu instanceof UploadedFile) {
            throw new RuntimeException('Foto dokumentasi 1 tidak terkirim ke server.');
        }

        if (($data['has_foto_dua'] ?? false) && ! $fotoDua instanceof UploadedFile) {
            throw new RuntimeException('Foto dokumentasi 2 tidak terkirim ke server.');
        }

        if (($data['has_foto_satu'] ?? false) && $fotoSatu instanceof UploadedFile && ! $this->isUsableUploadedFile($fotoSatu)) {
            throw new RuntimeException('Foto dokumentasi 1 diterima, tetapi file temporary-nya tidak bisa dipakai.');
        }

        if (($data['has_foto_dua'] ?? false) && $fotoDua instanceof UploadedFile && ! $this->isUsableUploadedFile($fotoDua)) {
            throw new RuntimeException('Foto dokumentasi 2 diterima, tetapi file temporary-nya tidak bisa dipakai.');
        }
    }

    /**
     * @param  array<int, string>  $placeholders
     */
    private function normalizeTemplatePlaceholders(string $templatePath, array $placeholders): void
    {
        $archive = new ZipArchive;

        if ($archive->open($templatePath) !== true) {
            throw new RuntimeException('Gagal membuka template DOCX.');
        }

        $documentXml = $archive->getFromName('word/document.xml');

        if (! is_string($documentXml)) {
            $archive->close();

            throw new RuntimeException('Isi template DOCX tidak lengkap.');
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = true;
        $document->formatOutput = false;
        $document->loadXML($documentXml);

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('w', self::WORD_NAMESPACE);

        $targetTexts = array_map(fn (string $placeholder): string => '${'.$placeholder.'}', $placeholders);
        $paragraphs = $xpath->query('//w:p');

        if ($paragraphs !== false) {
            foreach ($paragraphs as $paragraph) {
                if (! $paragraph instanceof DOMElement) {
                    continue;
                }

                $textNodes = $xpath->query('.//w:t', $paragraph);

                if ($textNodes === false || $textNodes->length === 0) {
                    continue;
                }

                $paragraphText = '';

                foreach ($textNodes as $textNode) {
                    $paragraphText .= $textNode->textContent;
                }

                if (! in_array($paragraphText, $targetTexts, true)) {
                    continue;
                }

                $paragraphProperties = $xpath->query('./w:pPr', $paragraph)?->item(0);
                $runProperties = $xpath->query('.//w:rPr', $paragraph)?->item(0);

                while ($paragraph->firstChild !== null) {
                    $paragraph->removeChild($paragraph->firstChild);
                }

                if ($paragraphProperties instanceof DOMElement) {
                    $paragraph->appendChild($paragraphProperties->cloneNode(true));
                }

                $run = $document->createElementNS(self::WORD_NAMESPACE, 'w:r');

                if ($runProperties instanceof DOMElement) {
                    $run->appendChild($runProperties->cloneNode(true));
                }

                $text = $document->createElementNS(self::WORD_NAMESPACE, 'w:t');
                $text->setAttribute('xml:space', 'preserve');
                $text->nodeValue = $paragraphText;
                $run->appendChild($text);
                $paragraph->appendChild($run);
            }
        }

        $archive->addFromString('word/document.xml', $document->saveXML() ?: '');
        $archive->close();
    }

    /**
     * @param  array<int, string>  $temporaryImagePaths
     * @return array{path: string, extension: string, content_type: string}
     */
    private function prepareUploadedImage(UploadedFile $file, array &$temporaryImagePaths): array
    {
        $sourcePath = $this->resolveUploadedFilePath($file);

        if ($sourcePath === null) {
            throw new RuntimeException('File gambar sementara tidak ditemukan.');
        }

        $imageBinary = file_get_contents($sourcePath);

        if ($imageBinary === false) {
            throw new RuntimeException('Gagal membaca file gambar yang diupload.');
        }

        $imageInfo = getimagesizefromstring($imageBinary);

        if ($imageInfo === false || ! isset($imageInfo['mime'])) {
            throw new RuntimeException('Gambar yang diupload tidak dapat diproses.');
        }

        $contentType = strtolower((string) $imageInfo['mime']);
        $extension = $this->extensionFromMimeType($contentType);
        $temporaryImagePath = $this->createTemporaryImagePath($extension);

        if (file_put_contents($temporaryImagePath, $imageBinary) === false) {
            throw new RuntimeException('Gagal menyalin file gambar sementara.');
        }

        $temporaryImagePaths[] = $temporaryImagePath;

        return [
            'path' => $temporaryImagePath,
            'extension' => $extension,
            'content_type' => $contentType,
        ];
    }

    private function isUsableUploadedFile(UploadedFile $file): bool
    {
        return $this->resolveUploadedFilePath($file) !== null;
    }

    private function resolveUploadedFilePath(UploadedFile $file): ?string
    {
        $candidates = [
            $file->getPathname(),
            $file->getRealPath() ?: null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param  array<string, array{path: string, extension: string, content_type: string}|null>  $imageMap
     * @param  array<string, mixed>  $debug
     */
    private function injectDocumentationImages(string $docxPath, array $imageMap, array &$debug): void
    {
        $archive = new ZipArchive;

        if ($archive->open($docxPath) !== true) {
            throw new RuntimeException('Gagal membuka hasil dokumen DOCX.');
        }

        $documentXml = $archive->getFromName('word/document.xml');
        $relationshipsXml = $archive->getFromName('word/_rels/document.xml.rels');
        $contentTypesXml = $archive->getFromName('[Content_Types].xml');

        if (! is_string($documentXml) || ! is_string($relationshipsXml) || ! is_string($contentTypesXml)) {
            $archive->close();

            throw new RuntimeException('Struktur hasil DOCX tidak lengkap.');
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = true;
        $document->formatOutput = false;
        $document->loadXML($documentXml);

        $relationships = new DOMDocument('1.0', 'UTF-8');
        $relationships->preserveWhiteSpace = true;
        $relationships->formatOutput = false;
        $relationships->loadXML($relationshipsXml);

        $contentTypes = new DOMDocument('1.0', 'UTF-8');
        $contentTypes->preserveWhiteSpace = true;
        $contentTypes->formatOutput = false;
        $contentTypes->loadXML($contentTypesXml);

        $documentXPath = new DOMXPath($document);
        $documentXPath->registerNamespace('w', self::WORD_NAMESPACE);

        $relationshipsXPath = new DOMXPath($relationships);
        $relationshipsXPath->registerNamespace('rel', self::PACKAGE_RELATIONSHIP_NAMESPACE);

        $contentTypesXPath = new DOMXPath($contentTypes);
        $contentTypesXPath->registerNamespace('ct', self::CONTENT_TYPE_NAMESPACE);

        $nextRelationshipId = $this->nextRelationshipId($relationshipsXPath);
        $imageIndex = 1;
        $debug['placeholder_results'] = [];

        foreach ($imageMap as $placeholder => $imageData) {
            $placeholderNodes = $documentXPath->query(sprintf('//w:t[text()="${%s}"]', $placeholder));
            $placeholderCount = $placeholderNodes === false ? 0 : $placeholderNodes->length;
            $debug['placeholder_results'][$placeholder] = [
                'placeholder_count' => $placeholderCount,
                'image_data' => is_array($imageData)
                    ? [
                        'path' => $imageData['path'],
                        'path_exists' => is_file($imageData['path']),
                        'extension' => $imageData['extension'],
                        'content_type' => $imageData['content_type'],
                    ]
                    : null,
                'inserted' => false,
            ];

            if ($placeholderNodes === false || $placeholderNodes->length === 0) {
                continue;
            }

            foreach ($placeholderNodes as $placeholderNode) {
                $paragraphNode = $this->findAncestorElement($placeholderNode, 'p');

                if (! $paragraphNode instanceof DOMElement) {
                    continue;
                }

                $replacementXml = $this->buildEmptyParagraphXml();

                if (is_array($imageData) && is_file($imageData['path'])) {
                    $relationshipId = 'rId'.$nextRelationshipId;
                    $nextRelationshipId++;

                    $imageName = 'generated-image-'.$imageIndex.'.'.$imageData['extension'];
                    $imageIndex++;

                    $imageBinary = file_get_contents($imageData['path']);

                    if ($imageBinary === false) {
                        throw new RuntimeException('Gagal membaca gambar dokumentasi sementara.');
                    }

                    $archive->addFromString('word/media/'.$imageName, $imageBinary);
                    $this->appendImageRelationship($relationships, $relationshipId, $imageName);
                    $this->ensureImageContentType(
                        $contentTypes,
                        $contentTypesXPath,
                        $imageData['extension'],
                        $imageData['content_type'],
                    );

                    [$widthEmu, $heightEmu] = $this->calculateImageDimensions($imageData['path']);
                    $replacementXml = $this->buildImageParagraphXml($relationshipId, $widthEmu, $heightEmu);
                    $debug['placeholder_results'][$placeholder]['inserted'] = true;
                    $debug['placeholder_results'][$placeholder]['relationship_id'] = $relationshipId;
                    $debug['placeholder_results'][$placeholder]['image_name'] = $imageName;
                }

                $fragment = $document->createDocumentFragment();
                $fragment->appendXML($replacementXml);
                $paragraphNode->parentNode?->replaceChild($fragment, $paragraphNode);
            }
        }

        $archive->addFromString('word/document.xml', $document->saveXML() ?: '');
        $archive->addFromString('word/_rels/document.xml.rels', $relationships->saveXML() ?: '');
        $archive->addFromString('[Content_Types].xml', $contentTypes->saveXML() ?: '');
        $debug['docx_entries_after_injection'] = $this->collectArchiveEntries($archive);
        $archive->close();
    }

    private function describeUploadedFile(?UploadedFile $file): ?array
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        $resolvedPath = $this->resolveUploadedFilePath($file);

        return [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'client_extension' => $file->getClientOriginalExtension(),
            'pathname' => $file->getPathname(),
            'real_path' => $file->getRealPath() ?: null,
            'resolved_path' => $resolvedPath,
            'resolved_path_exists' => is_string($resolvedPath) && is_file($resolvedPath),
            'size' => $file->getSize(),
        ];
    }

    private function writeDebugSnapshot(array $debug): void
    {
        $debugDirectory = storage_path('app/private/tool-debug');
        File::ensureDirectoryExists($debugDirectory);
        File::put(
            $debugDirectory.'/last-generator-dokumen-debug.json',
            json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        );
    }

    /**
     * @return array<int, array{name: string, size: int}>
     */
    private function collectArchiveEntries(ZipArchive $archive): array
    {
        $entries = [];

        for ($index = 0; $index < $archive->numFiles; $index++) {
            $stat = $archive->statIndex($index);

            if ($stat === false || ! isset($stat['name'], $stat['size'])) {
                continue;
            }

            $entries[] = [
                'name' => (string) $stat['name'],
                'size' => (int) $stat['size'],
            ];
        }

        return $entries;
    }

    private function nextRelationshipId(DOMXPath $relationshipsXPath): int
    {
        $nodes = $relationshipsXPath->query('//rel:Relationship');
        $max = 0;

        if ($nodes === false) {
            return 8;
        }

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $numeric = (int) preg_replace('/[^0-9]/', '', $node->getAttribute('Id'));
            $max = max($max, $numeric);
        }

        return $max + 1;
    }

    private function appendImageRelationship(DOMDocument $relationships, string $relationshipId, string $imageName): void
    {
        $relationship = $relationships->createElementNS(self::PACKAGE_RELATIONSHIP_NAMESPACE, 'Relationship');
        $relationship->setAttribute('Id', $relationshipId);
        $relationship->setAttribute('Type', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image');
        $relationship->setAttribute('Target', 'media/'.$imageName);
        $relationships->documentElement?->appendChild($relationship);
    }

    private function ensureImageContentType(
        DOMDocument $contentTypes,
        DOMXPath $contentTypesXPath,
        string $extension,
        string $contentType,
    ): void {
        $existing = $contentTypesXPath->query(sprintf('//ct:Default[@Extension="%s"]', $extension));

        if ($existing !== false && $existing->length > 0) {
            return;
        }

        $default = $contentTypes->createElementNS(self::CONTENT_TYPE_NAMESPACE, 'Default');
        $default->setAttribute('Extension', $extension);
        $default->setAttribute('ContentType', $contentType);
        $contentTypes->documentElement?->appendChild($default);
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function calculateImageDimensions(string $imagePath): array
    {
        $size = getimagesize($imagePath);
        $maxWidth = 540;
        $maxHeight = 320;

        if ($size === false) {
            return [$maxWidth * 9525, $maxHeight * 9525];
        }

        $width = max(1, (int) $size[0]);
        $height = max(1, (int) $size[1]);
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);

        return [
            (int) round($width * $scale * 9525),
            (int) round($height * $scale * 9525),
        ];
    }

    private function buildImageParagraphXml(string $relationshipId, int $widthEmu, int $heightEmu): string
    {
        $docPrId = random_int(1000, 999999);
        $wordNamespace = self::WORD_NAMESPACE;
        $wordDrawingNamespace = self::WORD_DRAWING_NAMESPACE;
        $drawingNamespace = self::DRAWING_NAMESPACE;
        $pictureNamespace = self::PICTURE_NAMESPACE;
        $relationshipNamespace = self::RELATIONSHIP_NAMESPACE;

        return <<<XML
<w:p xmlns:w="{$wordNamespace}" xmlns:wp="{$wordDrawingNamespace}" xmlns:a="{$drawingNamespace}" xmlns:pic="{$pictureNamespace}" xmlns:r="{$relationshipNamespace}">
  <w:pPr>
    <w:jc w:val="center"/>
    <w:spacing w:after="120" w:line="240" w:lineRule="auto"/>
  </w:pPr>
  <w:r>
    <w:drawing>
      <wp:inline distT="0" distB="0" distL="0" distR="0">
        <wp:extent cx="{$widthEmu}" cy="{$heightEmu}"/>
        <wp:docPr id="{$docPrId}" name="Documentation Image"/>
        <wp:cNvGraphicFramePr>
          <a:graphicFrameLocks noChangeAspect="1"/>
        </wp:cNvGraphicFramePr>
        <a:graphic>
          <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
            <pic:pic>
              <pic:nvPicPr>
                <pic:cNvPr id="0" name="Documentation Image"/>
                <pic:cNvPicPr/>
              </pic:nvPicPr>
              <pic:blipFill>
                <a:blip r:embed="{$relationshipId}"/>
                <a:stretch><a:fillRect/></a:stretch>
              </pic:blipFill>
              <pic:spPr>
                <a:xfrm>
                  <a:off x="0" y="0"/>
                  <a:ext cx="{$widthEmu}" cy="{$heightEmu}"/>
                </a:xfrm>
                <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
              </pic:spPr>
            </pic:pic>
          </a:graphicData>
        </a:graphic>
      </wp:inline>
    </w:drawing>
  </w:r>
</w:p>
XML;
    }

    private function buildEmptyParagraphXml(): string
    {
        $wordNamespace = self::WORD_NAMESPACE;

        return <<<XML
<w:p xmlns:w="{$wordNamespace}">
  <w:r>
    <w:t></w:t>
  </w:r>
</w:p>
XML;
    }

    private function findAncestorElement(DOMNode $node, string $localName): ?DOMElement
    {
        $currentNode = $node->parentNode;

        while ($currentNode !== null) {
            if ($currentNode instanceof DOMElement && $currentNode->localName === $localName) {
                return $currentNode;
            }

            $currentNode = $currentNode->parentNode;
        }

        return null;
    }

    private function createTemporaryDocxPath(): string
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'tool-docx-');

        if ($temporaryPath === false) {
            throw new RuntimeException('Gagal membuat file sementara.');
        }

        @unlink($temporaryPath);

        return $temporaryPath.'.docx';
    }

    private function extensionFromMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => throw new RuntimeException('Format gambar tidak didukung untuk dokumen Word.'),
        };
    }

    private function createTemporaryImagePath(string $extension): string
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'tool-img-');

        if ($temporaryPath === false) {
            throw new RuntimeException('Gagal membuat file gambar sementara.');
        }

        @unlink($temporaryPath);

        return $temporaryPath.'.'.Str::lower($extension);
    }
}
