<table style="text-align: center">
    <thead>
        <tr>
            <th style="width: 33%">Rank</th>
            <th style="width: 33%">ID</th>
            <th style="width: 33%">Performance Rating</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 0; $i < count($players); $i++): ?>
            <?php $player = $players[$i]; ?>
            <tr><?php include __DIR__ . '/player.html.php'; ?></tr>
        <?php endfor; ?>
    </tbody>
</table>
