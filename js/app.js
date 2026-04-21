document.addEventListener("DOMContentLoaded", () => {
  const liveTime = document.getElementById("liveTime");

  const updateTime = () => {
    const now = new Date();
    const hh = String(now.getHours()).padStart(2, "0");
    const mm = String(now.getMinutes()).padStart(2, "0");
    const ss = String(now.getSeconds()).padStart(2, "0");
    if (liveTime) {
      liveTime.textContent = `${hh}:${mm}:${ss}`;
    }
  };

  updateTime();
  setInterval(updateTime, 1000);

  document.querySelectorAll(".metric-card").forEach((card, index) => {
    card.style.animationDelay = `${index * 90}ms`;
  });
});
