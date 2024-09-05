<!-- modal.php -->
<!-- Modal -->
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p>We value your feedback! Would you like to give us your feedback?</p>
    <div class="modal-buttons">
      <!-- Pass the correct current URL using PHP -->
      <a href="feedback.php?redirect_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
        <button>Yes, Sure</button>
      </a>
      <button class="cancel">Cancel</button>
    </div>
  </div>
</div>
