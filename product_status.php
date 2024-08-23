<td align="center">
    <?php
    // Determine the badge class based on the status
    $badge_class = ($row['prod_status_desc'] === 'เบิกสินค้าได้') ? 'badge bg-success' : 'badge bg-danger';
    ?>
    <span class="<?php echo $badge_class; ?>"><?php echo $row['prod_status_desc']; ?></span>
</td>