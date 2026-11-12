<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use ZipArchive;

class CustomerImportService
{
    /**
     * Expected CSV / Excel header definitions.
     */
    public const HEADERS = [
        'company_name',
        'customer_code',
        'contact_person',
        'phone',
        'alt_phone',
        'email',
        'category',
        'territory',
        'address',
        'city',
        'state',
        'landmark',
        'credit_limit',
        'payment_terms',
        'assigned_agent_code',
        'notes',
    ];

    /**
     * Generate sample CSV template content.
     */
    public function generateSampleCsv(): string
    {
        $headers = [
            'Company / Store Name',
            'Customer Code (Optional)',
            'Contact Person / Proprietor',
            'Primary Phone',
            'Alternative Phone',
            'Email Address',
            'Category (retailer, wholesaler, electrical_contractor, lighting_showroom, supermarket, real_estate_developer, walk_in)',
            'Territory / Zone',
            'Physical Address',
            'City',
            'State',
            'Prominent Landmark',
            'Credit Limit (NGN)',
            'Payment Terms (cash_on_delivery, net_7, net_14, net_30)',
            'Assigned Agent Code (Optional, e.g. REL-FE-101)',
            'Notes / Remarks',
        ];

        $sampleRows = [
            [
                'Supreme Voltage Electricals Ltd',
                'CUST-ALB-101',
                'Chief Emeka Okoro',
                '+234 802 334 1122',
                '+234 814 556 7788',
                'supreme.voltage@gmail.com',
                'retailer',
                'Alaba International Market',
                'Block 12 Shop 4, Electronics Section, Alaba Market, Ojo',
                'Lagos',
                'Lagos State',
                'Beside Access Bank Alaba Branch',
                '3500000',
                'net_7',
                'REL-FE-103',
                'Manual ledger migration - bulk importer of Schneider breakers & wiring.',
            ],
            [
                'Lekki Modern Lighting & Interior Showroom',
                'CUST-LKK-102',
                'Mrs. Folashade Adeyemi',
                '+234 803 778 9900',
                '',
                'folashade@lekki-lights.ng',
                'lighting_showroom',
                'Lekki Phase 1 Corridor',
                'Plot 22 Admiralty Way, Lekki Phase 1',
                'Lagos',
                'Lagos State',
                'Opposite Domino Pizza Admiralty',
                '8000000',
                'net_14',
                'REL-FE-102',
                'High-end residential lighting studio for luxury Lekki duplexes.',
            ],
            [
                'De-Godwin Power & Wiring Hub',
                '',
                'Engr. Godwin Sunday',
                '+234 805 112 4433',
                '',
                '',
                'electrical_contractor',
                'Ikeja Commercial Hub',
                '18 Medical Road, Computer Village / Ikeja',
                'Ikeja',
                'Lagos State',
                'Near Ikeja Under-bridge',
                '0',
                'cash_on_delivery',
                'REL-FE-101',
                'Contractor purchasing wholesale cables and 3-phase DB panels.',
            ],
            [
                'Gbagada General Cash & Carry Buyers',
                '',
                'Mallam Sani Haruna',
                '+234 809 223 3445',
                '',
                '',
                'retailer',
                'Gbagada / Bariga Zone',
                '45 Diya Street, Ifako-Gbagada',
                'Lagos',
                'Lagos State',
                'Opposite Deeper Life HQ',
                '0',
                'cash_on_delivery',
                '',
                'Unassigned walk-in/counter buyer - assign to available territory rep.',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        // Add UTF-8 BOM so Excel opens with proper encoding
        fputs($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Parse uploaded file (CSV or XLSX).
     *
     * @return array<int, array<string, mixed>>
     */
    public function parseFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            return $this->parseXlsx($file->getRealPath());
        }

        return $this->parseCsv($file->getRealPath());
    }

    /**
     * Parse CSV file.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseCsv(string $filePath): array
    {
        $rows = [];
        if (!file_exists($filePath)) {
            return $rows;
        }

        $handle = @fopen($filePath, 'r');
        if (!$handle) {
            return $rows;
        }

        // Check and strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headerRow = fgetcsv($handle);
        if (!$headerRow) {
            fclose($handle);
            return $rows;
        }

        $normalizedHeaders = array_map([$this, 'normalizeHeaderKey'], $headerRow);

        while (($data = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($data, fn($v) => trim((string)$v) !== ''))) {
                continue;
            }

            $row = [];
            foreach ($normalizedHeaders as $idx => $key) {
                if ($key && isset($data[$idx])) {
                    $row[$key] = trim((string)$data[$idx]);
                }
            }
            if (!empty($row)) {
                $rows[] = $row;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse XLSX file using built-in ZipArchive and SimpleXML.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return $this->parseCsv($filePath);
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $stringsXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXmlContent !== false) {
            $stringsXml = simplexml_load_string($stringsXmlContent);
            if ($stringsXml && isset($stringsXml->si)) {
                foreach ($stringsXml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read sheet1
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXmlContent === false) {
            return [];
        }

        $sheetXml = simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rawGrid = [];
        foreach ($sheetXml->sheetData->row as $rowNode) {
            $rowNum = (int)$rowNode['r'];
            $rowData = [];

            foreach ($rowNode->c as $cell) {
                $coord = (string)$cell['r']; // e.g. "A1", "B2"
                $colLetter = preg_replace('/[0-9]/', '', $coord);
                $colIndex = $this->columnLetterToIndex($colLetter);

                $cellType = (string)$cell['t'];
                $val = isset($cell->v) ? (string)$cell->v : '';

                if ($cellType === 's' && isset($sharedStrings[(int)$val])) {
                    $cellValue = $sharedStrings[(int)$val];
                } elseif ($cellType === 'inlineStr' && isset($cell->is->t)) {
                    $cellValue = (string)$cell->is->t;
                } else {
                    $cellValue = $val;
                }

                $rowData[$colIndex] = trim($cellValue);
            }

            if (!empty($rowData)) {
                // Fill missing column indexes
                $maxCol = max(array_keys($rowData));
                $denseRow = [];
                for ($c = 0; $c <= $maxCol; $c++) {
                    $denseRow[$c] = $rowData[$c] ?? '';
                }
                $rawGrid[] = $denseRow;
            }
        }

        if (empty($rawGrid)) {
            return [];
        }

        $headerRow = array_shift($rawGrid);
        $normalizedHeaders = array_map([$this, 'normalizeHeaderKey'], $headerRow);

        $parsedRows = [];
        foreach ($rawGrid as $data) {
            if (empty(array_filter($data, fn($v) => trim((string)$v) !== ''))) {
                continue;
            }

            $row = [];
            foreach ($normalizedHeaders as $idx => $key) {
                if ($key && isset($data[$idx])) {
                    $row[$key] = trim((string)$data[$idx]);
                }
            }
            if (!empty($row)) {
                $parsedRows[] = $row;
            }
        }

        return $parsedRows;
    }

    /**
     * Convert Excel column letter (e.g., A, B, Z, AA) to zero-based index.
     */
    protected function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $index = 0;
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }
        return $index - 1;
    }

    /**
     * Map fuzzy header text to a standard column name.
     */
    public function normalizeHeaderKey(string $header): ?string
    {
        $clean = strtolower(trim($header));
        $clean = str_replace(['_', '-', '/', '(', ')', '.', '  '], ' ', $clean);

        if (str_contains($clean, 'company') || str_contains($clean, 'store name') || str_contains($clean, 'shop name') || str_contains($clean, 'business')) {
            return 'company_name';
        }
        if (str_contains($clean, 'agent') || str_contains($clean, 'rep') || str_contains($clean, 'assigned') || str_contains($clean, 'sales executive') || str_contains($clean, 'staff')) {
            return 'assigned_agent_code';
        }
        if (str_contains($clean, 'customer code') || str_contains($clean, 'cust code') || str_contains($clean, 'client code') || $clean === 'code') {
            return 'customer_code';
        }
        if (str_contains($clean, 'contact') || str_contains($clean, 'proprietor') || str_contains($clean, 'owner') || str_contains($clean, 'person')) {
            return 'contact_person';
        }
        if (str_contains($clean, 'alt phone') || str_contains($clean, 'second phone')) {
            return 'alt_phone';
        }
        if (str_contains($clean, 'phone') || str_contains($clean, 'mobile') || str_contains($clean, 'tel')) {
            return 'phone';
        }
        if (str_contains($clean, 'email') || str_contains($clean, 'mail')) {
            return 'email';
        }
        if (str_contains($clean, 'category') || str_contains($clean, 'type') || str_contains($clean, 'segment')) {
            return 'category';
        }
        if (str_contains($clean, 'territory') || str_contains($clean, 'corridor') || str_contains($clean, 'zone') || str_contains($clean, 'area')) {
            return 'territory';
        }
        if (str_contains($clean, 'landmark') || str_contains($clean, 'bus stop')) {
            return 'landmark';
        }
        if (str_contains($clean, 'address') || str_contains($clean, 'location') || str_contains($clean, 'street')) {
            return 'address';
        }
        if (str_contains($clean, 'city') || str_contains($clean, 'lga')) {
            return 'city';
        }
        if (str_contains($clean, 'state')) {
            return 'state';
        }
        if (str_contains($clean, 'credit limit') || str_contains($clean, 'credit')) {
            return 'credit_limit';
        }
        if (str_contains($clean, 'payment terms') || str_contains($clean, 'terms')) {
            return 'payment_terms';
        }
        if (str_contains($clean, 'note') || str_contains($clean, 'remark') || str_contains($clean, 'comment')) {
            return 'notes';
        }

        return null;
    }

    /**
     * Import parsed rows into database.
     *
     * @param array<int, array<string, mixed>> $rows
     * @param int|null $defaultAgentId
     * @return array{
     *     total: int,
     *     created: int,
     *     updated: int,
     *     unassigned: int,
     *     errors: array<int, string>,
     *     customers: array<int, Customer>
     * }
     */
    public function importRows(array $rows, ?int $defaultAgentId = null): array
    {
        $created = 0;
        $updated = 0;
        $unassigned = 0;
        $errors = [];
        $savedCustomers = [];

        // Preload active agents for fast matching by code, email or name
        $agents = User::where('status', 'active')->get();
        $agentsByCode = [];
        $agentsByEmail = [];
        $agentsByName = [];
        foreach ($agents as $agent) {
            if ($agent->agent_code) {
                $agentsByCode[strtoupper(trim($agent->agent_code))] = $agent->id;
            }
            if ($agent->email) {
                $agentsByEmail[strtolower(trim($agent->email))] = $agent->id;
            }
            $agentsByName[strtolower(trim($agent->name))] = $agent->id;
        }

        $latestId = Customer::max('id') ?? 100;
        $codeCounter = $latestId + 1;

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // Accounting for 1-based index and header row

            // Required validations
            $companyName = trim((string)($row['company_name'] ?? ''));
            $contactPerson = trim((string)($row['contact_person'] ?? ''));
            $phone = trim((string)($row['phone'] ?? ''));
            $territory = trim((string)($row['territory'] ?? ''));
            $address = trim((string)($row['address'] ?? ''));

            if ($companyName === '') {
                $errors[] = "Row {$rowNum}: Company / Store Name is required.";
                continue;
            }

            if ($contactPerson === '') {
                $contactPerson = 'Proprietor';
            }

            if ($phone === '') {
                $errors[] = "Row {$rowNum}: Phone Number is required for '{$companyName}'.";
                continue;
            }

            if ($territory === '') {
                $territory = 'Lagos Commercial Zone';
            }

            if ($address === '') {
                $address = $territory . ', Lagos State';
            }

            // Customer code handling
            $code = strtoupper(trim((string)($row['customer_code'] ?? '')));
            if ($code === '') {
                do {
                    $code = 'CUST-IMP-' . str_pad((string)$codeCounter, 4, '0', STR_PAD_LEFT);
                    $codeCounter++;
                } while (Customer::where('customer_code', $code)->exists());
            }

            // Category normalization
            $cat = strtolower(trim((string)($row['category'] ?? 'retailer')));
            $validCategories = ['retailer', 'wholesaler', 'electrical_contractor', 'lighting_showroom', 'supermarket', 'real_estate_developer', 'walk_in'];
            if (!in_array($cat, $validCategories, true)) {
                if (str_contains($cat, 'whole')) {
                    $cat = 'wholesaler';
                } elseif (str_contains($cat, 'contract') || str_contains($cat, 'install')) {
                    $cat = 'electrical_contractor';
                } elseif (str_contains($cat, 'light') || str_contains($cat, 'show')) {
                    $cat = 'lighting_showroom';
                } elseif (str_contains($cat, 'walk')) {
                    $cat = 'walk_in';
                } else {
                    $cat = 'retailer';
                }
            }

            // Payment terms normalization
            $terms = strtolower(trim((string)($row['payment_terms'] ?? 'cash_on_delivery')));
            $terms = str_replace(' ', '_', $terms);
            if (!in_array($terms, ['cash_on_delivery', 'net_7', 'net_14', 'net_30'], true)) {
                $terms = 'cash_on_delivery';
            }

            // Credit limit normalization
            $creditRaw = preg_replace('/[^0-9.]/', '', (string)($row['credit_limit'] ?? '0'));
            $creditLimit = (float)($creditRaw !== '' ? $creditRaw : 0);

            // Determine Assigned Sales Rep
            $assignedRepId = null;
            $agentRef = trim((string)($row['assigned_agent_code'] ?? ''));

            if ($agentRef !== '') {
                $upperRef = strtoupper($agentRef);
                $lowerRef = strtolower($agentRef);

                if (isset($agentsByCode[$upperRef])) {
                    $assignedRepId = $agentsByCode[$upperRef];
                } elseif (isset($agentsByEmail[$lowerRef])) {
                    $assignedRepId = $agentsByEmail[$lowerRef];
                } elseif (isset($agentsByName[$lowerRef])) {
                    $assignedRepId = $agentsByName[$lowerRef];
                }
            }

            if ($assignedRepId === null && $defaultAgentId) {
                $assignedRepId = $defaultAgentId;
            }

            if ($assignedRepId === null) {
                $unassigned++;
            }

            $customerData = [
                'company_name' => $companyName,
                'customer_code' => $code,
                'contact_person' => $contactPerson,
                'phone' => $phone,
                'alt_phone' => !empty($row['alt_phone']) ? trim((string)$row['alt_phone']) : null,
                'email' => !empty($row['email']) ? trim((string)$row['email']) : null,
                'category' => $cat,
                'territory' => $territory,
                'address' => $address,
                'city' => !empty($row['city']) ? trim((string)$row['city']) : 'Lagos',
                'state' => !empty($row['state']) ? trim((string)$row['state']) : 'Lagos State',
                'landmark' => !empty($row['landmark']) ? trim((string)$row['landmark']) : null,
                'credit_limit' => $creditLimit,
                'payment_terms' => $terms,
                'onboarded_by' => $assignedRepId,
                'onboarded_date' => Carbon::now()->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => !empty($row['notes']) ? trim((string)$row['notes']) : 'Bulk imported via spreadsheet.',
            ];

            $existing = Customer::where('customer_code', $code)->first();
            if ($existing) {
                $existing->update($customerData);
                $savedCustomers[] = $existing;
                $updated++;
            } else {
                $newCustomer = Customer::create($customerData);
                $savedCustomers[] = $newCustomer;
                $created++;
            }
        }

        return [
            'total' => count($rows),
            'created' => $created,
            'updated' => $updated,
            'unassigned' => $unassigned,
            'errors' => $errors,
            'customers' => $savedCustomers,
        ];
    }
}
