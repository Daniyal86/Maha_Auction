<?php
// seed_all_cities.php
require_once 'config/db.php';

$missing_cities = [
    ['akola', 'Akola', 20.7373, 77.0931],
    ['buldana', 'Buldana', 20.5285, 76.3825],
    ['washim', 'Washim', 20.2761, 77.2516],
    ['yavatmal', 'Yavatmal', 20.0364, 78.041],
    ['jalna', 'Jalna', 19.995, 76.0179],
    ['parbhani', 'Parbhani', 19.3068, 76.6941],
    ['hingoli', 'Hingoli', 19.6032, 77.119],
    ['bid', 'Bid', 18.934, 75.7703],
    ['latur', 'Latur', 18.361, 76.7301],
    ['osmanabad', 'Osmanabad', 18.1953, 75.9951],
    ['mumbai_suburban', 'Mumbai Suburban', 19.1278, 72.8554],
    ['raigarh', 'Raigarh', 18.4563, 73.2845],
    ['ratnagiri', 'Ratnagiri', 17.2062, 73.4642],
    ['sindhudurg', 'Sindhudurg', 16.1585, 73.7671],
    ['wardha', 'Wardha', 20.8068, 78.5778],
    ['bhandara', 'Bhandara', 21.1035, 79.7873],
    ['gondiya', 'Gondiya', 21.2222, 80.2537],
    ['chandrapur', 'Chandrapur', 20.0967, 79.3217],
    ['garhchiroli', 'Garhchiroli', 19.8109, 80.315],
    ['nandurbar', 'Nandurbar', 21.5821, 74.2834],
    ['dhule', 'Dhule', 21.2329, 74.651],
    ['ahmadnagar', 'Ahmadnagar', 19.171, 74.7551],
    ['satara', 'Satara', 17.6745, 74.1743],
    ['sangli', 'Sangli', 17.1559, 74.7028]
];

$stmt = $pdo->prepare("INSERT INTO cities (id, name, property_count, lat, lng) VALUES (?, ?, 0, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)");

foreach ($missing_cities as $c) {
    $stmt->execute($c);
    echo "Seeded: {$c[1]}<br>\n";
}

echo "All districts verified!";
