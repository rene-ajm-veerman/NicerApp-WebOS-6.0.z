$doc = [
    "name" => "Alice",
"age" => 28,
"tags" => ["php", "mongodb", "laravel"],
"address" => ["city" => "Berlin", "country" => "Germany"],
"comments" => [
    ["user" => "bob", "likes" => 15, "text" => "Awesome!"],
["user" => "charlie", "likes" => 3]
],
"score" => 95
];

// Matching
$query = [
    "age" => ["$gte" => 18],
"tags" => ["$all" => ["php"]],
"comments" => ["$elemMatch" => ["likes" => ["$gte" => 10]]]
];

var_dump(MongoDBTools::matches($doc, $query)); // true

// Update
$update = [
    '$set' => ["status" => "active", "address.city" => "Munich"],
'$inc' => ["score" => 5],
'$push' => ["tags" => "symfony"],
'$pull' => ["comments" => ["likes" => ["$lt" => 10]]]
];

$newDoc = MongoDBTools::applyUpdate($doc, $update);
print_r($newDoc);
