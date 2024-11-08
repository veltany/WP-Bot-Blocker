function openTab(evt, tabName) {
    const tabLinks = document.getElementsByClassName("wpbb-tab-link");
    const tabContents = document.getElementsByClassName("wpbb-tab-content");

    // Hide all tab contents and remove "active" class from all tab links
    Array.from(tabContents).forEach(content => content.classList.remove("active"));
    Array.from(tabLinks).forEach(link => link.classList.remove("active"));

    // Show the current tab and add "active" class to the tab link
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}
