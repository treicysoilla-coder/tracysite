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

<style>
    /* High-Visibility Button Styles [cite: 2026-03-02] */
    .btn-reply-v2 { 
        color: #28a745 !important; 
        border: 1px solid #28a745 !important; 
        padding: 6px 14px; 
        border-radius: 6px; 
        text-decoration: none; 
        font-size: 11px; 
        font-weight: bold; 
        transition: 0.3s;
    }
    .btn-reply-v2:hover { background: #28a745; color: white !important; }

    .btn-delete-v2 { 
        color: #dc3545 !important; 
        border: 1px solid #dc3545 !important; 
        padding: 6px 14px; 
        border-radius: 6px; 
        text-decoration: none; 
        font-size: 11px; 
        font-weight: bold; 
        transition: 0.3s;
    }
    .btn-delete-v2:hover { background: #dc3545; color: white !important; }
    
    .msg-table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid rgba(255,255,255,0.05); }
</style>

<div class="inner-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: white;">Customer <span style="color: #ff9100;">Messages</span></h2>
        
        <form action="Dashboard.php" method="GET" style="display: flex; gap: 10px;">
            <input type="hidden" name="page" value="messages">
            <input type="text" name="search" class="search-bar" 
                   placeholder="Search name or email..." 
                   value="<?= htmlspecialchars($search) ?>" 
                   style="width: 250px; background: rgba(0,0,0,0.3); border: 1px solid #333; color: white; padding: 8px; border-radius: 5px;">
            <button type="submit" style="background: #55ff00; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">Search</button>
            <?php if(!empty($search)): ?>
                <a href="Dashboard.php?page=messages" style="color: #888; font-size: 12px; align-self: center;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(mysqli_num_rows($msg_result) > 0): ?>
        <table class="msg-table" style="width: 100%; border-collapse: collapse; color: white;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #0066FF; background: rgba(0,102,255,0.1);">
                    <th style="padding: 15px;">Date</th>
                    <th style="padding: 15px;">Name</th>
                    <th style="padding: 15px;">Email</th>
                    <th style="padding: 15px;">Message</th>
                    <th style="padding: 15px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($msg = mysqli_fetch_assoc($msg_result)): ?>
                    <tr>
                        <td style="white-space: nowrap; font-size: 12px; color: #888;"><?= date('M d, g:i a', strtotime($msg['created_at'])) ?></td>
                        <td><strong><?= htmlspecialchars($msg['name']) ?></strong></td>
                        <td style="color: rgb(217, 255, 0);"><?= htmlspecialchars($msg['email']) ?></td>
                        <td style="max-width: 400px; color: #ccc; font-size: 13px;"><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="mailto:<?= $msg['email'] ?>" class="btn-reply-v2">REPLY</a>
                                
                                <a href="delete_message.php?id=<?= $msg['id'] ?>" 
                                   class="btn-delete-v2" 
                                   onclick="return confirm('Permanently delete this message?')">DELETE</a>
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