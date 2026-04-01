<?php

namespace App\Services\reports;

use App\Models\DesignationsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\PaperUploadsModel;
use CodeIgniter\Config\BaseService;


class PrecisReportServices extends BaseService
{
    function __construct()
    {
        helper('array');
    }


    public function precis_report($acceptance_type, $exportName){
        $podiumPapers = (new PapersModel())->activePapersWithAcceptanceFilter($acceptance_type);
        $podiumPapersIds = array_column($podiumPapers, 'abstract_id');

        // Fetch all authors and paper uploads for all podium papers in one query
        $paperAuthorsMapped = [];
        $paperUploadsMapped = [];
        if (!empty($podiumPapersIds)) {
            $allAuthors = (new PaperAuthorsModel())->getAuthorsWithProfiles($podiumPapersIds);

            //fetch all uploads
            $allPaperUploads = (new PaperUploadsModel())->findAll();

            foreach ($allAuthors as $author) {
                $paperAuthorsMapped[$author['paper_id']][] = $author;
            }

            foreach ($allPaperUploads as $paperUpload){
                $paperUploadsMapped[$paperUpload['paper_id']][] = $paperUpload;
            }
        }

        // Load designations once
        $designations = (new DesignationsModel())->findAll();
        $designationsColumn = array_column($designations, 'name', 'id');

        // Map designation names to authors
        $mappedAuthors = [];
        foreach ($paperAuthorsMapped as $paperId => $authors) {
            $mappedAuthors[$paperId] = array_map(function ($author) use ($designationsColumn) {
                $designationNames = [];
                if (!empty($author['designations'])) {
                    $authorDesignations = json_decode($author['designations'], true) ?: [];
                    foreach ($authorDesignations as $designationId) {
                        if (isset($designationsColumn[$designationId])) {
                            $designationSelected = $designationsColumn[$designationId];
                            if(strtolower($designationSelected) === 'other')
                                $designationNames[] = $author['other_designation'];
                            else if(strtolower($designationSelected) === 'none')
                                $designationNames[] = '';
                            else
                                $designationNames[] = $designationSelected;
                        }
                    }
                }
                $author['designation_names'] = $designationNames;
                return $author;
            }, $authors);
        }


        // Initialize PHPWord document
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection([
            'marginLeft'   => 1440, // 1 inch
            'marginRight'  => 1440,
            'marginTop'    => 1440,
            'marginBottom' => 1440,
        ]);

        // Define styles
        $titleStyle = ['name' => 'Tahoma', 'size' => 12, 'bold' => true];
        $labelStyle = ['name' => 'Calibri', 'size' => 12, 'bold' => true];
        $textStyle  = ['name' => 'Calibri', 'size' => 12];

        if (!empty($podiumPapers)) {
            $rowCount = 0;

            foreach ($podiumPapers as $podiumPaper) {
                $rowCount++;

                if (empty(trim($podiumPaper['title']))) {
                    continue;
                }

                // Paper title with number
                $section->addText(
                    $podiumPaper['assigned_id'] . '. ' . ($podiumPaper['title']),
                    $titleStyle
                );

                // Build valid authors list
                $authorItems = [];
                if (!empty($mappedAuthors[$podiumPaper['abstract_id']])) {
                    foreach ($mappedAuthors[$podiumPaper['abstract_id']] as $item) {
                        // Skip removed authors
                        if ($item['is_removed'] !== '0') {
                            continue;
                        }

                        // Skip if no valid name
                        $surname = trim($item['user_surname'] ?? '');
                        $name    = trim($item['user_name'] ?? '');
                        if ($surname === '' && $name === '') {
                            continue;
                        }

                        // Middle initial (uppercase first letter + dot)
                        $middleInitial = '';
                        if (!empty(trim($item['user_middle'] ?? ''))) {
                            $middle = trim($item['user_middle']);
                            $middleInitial = ' ' . strtoupper(substr($middle, 0, 1)) . '.';
                        }

                        $fullName = $name . $middleInitial.' '.$surname;

                        $authorText = $fullName;
                        if (!empty($item['designation_names'])) {
                            $authorText .= ', '. implode(', ', $item['designation_names']);
                        }

                        $authorItems[] = [
                            'text'       => $authorText,
                            'presenting' => ($item['is_presenting_author'] === 'Yes')
                        ];
                    }
                }

                // Authors line
                $textRun = $section->addTextRun();
                $textRun->addText('Authors: ', $labelStyle);

                if (!empty($authorItems)) {
                    foreach ($authorItems as $index => $authorData) {
                        $style = $textStyle;
                        if ($authorData['presenting']) {
                            $style['italic'] = true;
                            $style['underline'] = 'single'; // Highlight presenting author
                        }

                        $separator = ($index < count($authorItems) - 1) ? '; ' : '';
                        $textRun->addText($authorData['text'] . $separator, $style);
                    }
                } else {
                    $textRun->addText('N/A', $textStyle);
                }

                // Abstract sections (display only if content exists and not "n/a")
                $abstractSections = [
                    'Hypothesis'   => $podiumPaper['hypothesis'],
                    'Design'       => $podiumPaper['study_design'],
                    'Introduction' => $podiumPaper['introduction'],
                    'Methods'      => $podiumPaper['methods'],
                    'Results'      => $podiumPaper['results'],
                    'Conclusion'   => $podiumPaper['conclusions']
                ];

                foreach ($abstractSections as $label => $content) {
                    $content = trim($content ?? '');
                    if ($content !== '' && strtolower($content) !== 'n/a') {
                        $textRun = $section->addTextRun();
                        $textRun->addText($label . ': ', $labelStyle);
                        $textRun->addTextBreak(1);               // ← key line
                        $textRun->addText($content, $textStyle);
                    }
                }

                // Add uploaded images
                if (!empty($paperUploadsMapped[$podiumPaper['abstract_id']])) {
                    $imageCount = 0;
                    foreach ($paperUploadsMapped[$podiumPaper['abstract_id']] as $upload) {
                        $imageCount++;

                        // Check if file exists and is an image
                        $filePath = ($upload['file_path'].$upload['file_name']) ? FCPATH.$upload['file_path'].$upload['file_name'] : ''; // Adjust this based on your actual field name

                        if (!empty($filePath) && file_exists($filePath)) {
                            // Check if it's an image file
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'tif'];
                            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                            if (in_array($fileExtension, $imageExtensions)) {
                                // Add a section break before images
                                $section->addTextBreak();
                                // Add the image to the document with appropriate sizing
                                try {
                                    // Add the image with specified dimensions
                                    // You can adjust width/height as needed
                                    $section->addImage(
                                        $filePath,
                                        [
                                            'width' => 400, // Adjust width in pixels (e.g., 400px)
                                            'height' => 300, // Adjust height in pixels
                                            'align' => 'left', // or 'center', 'right'
                                            'wrappingStyle' => 'inline' // or 'square', 'tight', 'behind', 'infront'
                                        ]
                                    );
                                } catch (\Exception $e) {
                                    // If image cannot be added, show an error message
                                    $section->addText('Error loading image: ' . $upload['file_name'], $textStyle);
                                }
                            } else {
                                // For non-image files, just show the filename
                                $section->addTextBreak();
                                $textRun = $section->addTextRun();
                                $textRun->addText('File ' . $imageCount . ':', $labelStyle);
                                $textRun->addText($upload['file_name'] . ' (Not an image file)', $textStyle);
                                $section->addTextBreak();
                            }
                        }
                    }

                    //image caption
                    $textRun = $section->addTextRun();
                    $textRun->addText($podiumPaper['image_caption'], $textStyle);
                    $section->addTextBreak();
                }

                // Spacing between papers
                $section->addTextBreak(2);
            }
        }

        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        // ✅ Save ONLY to temp file — remove the Agenda.docx save
        $tempFile = tempnam(sys_get_temp_dir(), 'WordReport') . '.docx';
        $objWriter->save($tempFile);

        // ✅ Clear any accidental output buffer before sending headers
        if (ob_get_length()) ob_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="'.$exportName.'.docx"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        readfile($tempFile);
        unlink($tempFile);
    }
}
