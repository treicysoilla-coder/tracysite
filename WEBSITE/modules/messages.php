<?php
// Handle Search Query
$search = $_GET['search'] ?? '';
$search_query = "";

if (!empty($search)) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $search_query = "WHERE name LIKE '%$safe_search%' OR email LIKE '%$safe_search%'";
}

// Fetch messages from the contact_messages table
$msg_query = "SELECT * FROM contact_messages $search_query ORDER BY created_at DESC";
$msg_result = mysqli_query($conn, $msg_query);
?>

<div class="inner-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2>Customer <span>Messages</span></h2>
        
        <form action="Dashboard.php" method="GET" style="display: flex; gap: 10px;">
            <input type="hidden" name="page" value="messages">
            <input type="text" name="search" class="search-bar" 
                   placeholder="Search name or email..." 
                   value="<?= htmlspecialchars($search) ?>" 
                   style="width: 250px; border: 1px solid #333;">
            <button type="submit" class="btn-action btn-reply" style="border: none; cursor: pointer;">Search</button>
            <?php if(!empty($search)): ?>
                <a href="Dashboard.php?page=messages" style="color: #888; font-size: 12px; align-self: center;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(mysqli_num_rows($msg_result) > 0): ?>
        <table class="msg-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($msg = mysqli_fetch_assoc($msg_result)): ?>
                    <tr>
                        <td style="white-space: nowrap;"><?= date('M d, g:i a', strtotime($msg['created_at'])) ?></td>
                        <td><strong><?= htmlspecialchars($msg['name']) ?></strong></td>
                        <td><?= htmlspecialchars($msg['email']) ?></td>
                        <td style="max-width: 400px; color: #ccc;"><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="mailto:<?= $msg['email'] ?>" class="btn-action btn-reply">Reply</a>
                                <a href="delete_message.php?id=<?= $msg['id'] ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Permanently delete this message?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; color: #666; border: 1px dashed #333; border-radius: 15px;">
            <p>No messages found <?= !empty($search) ? "matching '$search'" : "yet" ?>.</p>
        </div>
    <?php endif; ?>
</div>