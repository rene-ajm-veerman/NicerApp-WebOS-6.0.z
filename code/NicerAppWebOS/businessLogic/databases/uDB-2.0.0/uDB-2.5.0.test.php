<?PHP
$db->setTable('users');

// Simple index
$db->createIndex(['username']);

// Compound index with name
$db->createIndex(
    ['email' => 1, 'createdAt' => -1],
    ['name' => 'idx_email_created']
);

// Unique index
$db->createIndex(
    ['username'],
    ['unique' => true, 'name' => 'uniq_username']
);
?>
