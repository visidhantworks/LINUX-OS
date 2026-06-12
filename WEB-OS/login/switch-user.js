   let showPower = 1;

  function togglePower() {
    if (showPower === 1) {
      document.getElementById("powerList").style.display = "block";
    } else {
      document.getElementById("powerList").style.display = "none";
    }

    showPower = showPower * -1;
  }
