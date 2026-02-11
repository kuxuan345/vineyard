document.getElementById('yes-button').addEventListener('click', function () {
  window.location.href = 'menu.php';
});

document.getElementById('no-button').addEventListener('click', function () {
  alert('You must be 21 years or older to access this site.');
});