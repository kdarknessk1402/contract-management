<?php
header('Content-Type: text/plain; charset=utf-8');

if (!isset($_POST['number'])) {
    echo '';
    exit;
}

function numberToVietnameseWords($number) {
    $number = (int)$number;
    
    if ($number == 0) return 'Không đồng';
    
    $units = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    $levels = ['', 'nghìn', 'triệu', 'tỷ'];
    
    $result = '';
    $level = 0;
    
    while ($number > 0) {
        $group = $number % 1000;
        if ($group > 0) {
            $groupWords = '';
            
            $hundreds = (int)($group / 100);
            $tens = (int)(($group % 100) / 10);
            $ones = $group % 10;
            
            if ($hundreds > 0) {
                $groupWords .= $units[$hundreds] . ' trăm ';
            }
            
            if ($tens > 1) {
                $groupWords .= $units[$tens] . ' mươi ';
                if ($ones == 1) {
                    $groupWords .= 'mốt ';
                } elseif ($ones > 0) {
                    $groupWords .= $units[$ones] . ' ';
                }
            } elseif ($tens == 1) {
                $groupWords .= 'mười ';
                if ($ones > 0) {
                    $groupWords .= $units[$ones] . ' ';
                }
            } else {
                if ($hundreds > 0 && $ones > 0) {
                    $groupWords .= 'lẻ ';
                }
                if ($ones == 5 && $level > 0) {
                    $groupWords .= 'lăm ';
                } elseif ($ones > 0) {
                    $groupWords .= $units[$ones] . ' ';
                }
            }
            
            $result = $groupWords . $levels[$level] . ' ' . $result;
        }
        
        $number = (int)($number / 1000);
        $level++;
    }
    
    $result = trim($result);
    $result = ucfirst($result) . ' đồng';
    
    return $result;
}

echo numberToVietnameseWords($_POST['number']);
?>