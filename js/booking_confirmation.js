// Auto-clear the session after showing confirmation
    setTimeout(function () {
      fetch('clear_session.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
      });
    }, 5000);