// modal.js
window.onload = function() {
  var modal = document.getElementById("myModal");
  var span = document.getElementsByClassName("close")[0];
  var cancelButton = document.querySelector(".modal-buttons .cancel");

  // Function to open the modal
  function openModal() {
    modal.style.display = "block";
  }
  
  // Function to close the modal
  function closeModal() {
    modal.style.display = "none";
  }

  // Check if feedback has already been submitted
  if (!localStorage.getItem('feedbackSubmitted')) {
    // Open the modal on page load
    openModal();

    // Close the modal when the user clicks on <span> (x) or the cancel button
    span.onclick = function() {
      closeModal();
    };
    cancelButton.onclick = function() {
      closeModal();
    };

    // Close the modal when the user clicks anywhere outside of the modal
    window.onclick = function(event) {
      if (event.target == modal) {
        closeModal();
      }
    };

    // Reopen the modal every 5 minutes
    setInterval(function() {
      openModal();
    }, 300000); // 300000 milliseconds = 5 minutes

    // Close the modal automatically after 10 seconds
    setTimeout(function() {
      closeModal();
    }, 5000);
  }
};

// Call this function after feedback is submitted to prevent modal from reopening
function markFeedbackSubmitted() {
  localStorage.setItem('feedbackSubmitted', 'true');
}
