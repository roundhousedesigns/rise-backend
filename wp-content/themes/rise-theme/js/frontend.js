/**
 * Frontend JavaScript
 *
 * @package rise
 */

(function () {
	"use strict";

	/**
	 * Network Partners Grid functionality
	 */
	function initNetworkPartners() {
		// Check if we're on the network partners page
		const networkPartnerGrid = document.querySelector(".network-partner-grid");
		if (!networkPartnerGrid) return;

		// Add content overlay to each partner
		const partners = networkPartnerGrid.querySelectorAll(".network-partner");

		partners.forEach((partner) => {
			// Get permalink from the title link
			const titleLink = partner.querySelector(".entry-title a");
			const permalink = titleLink ? titleLink.getAttribute("href") : "";
			
			// Create content overlay
			const contentOverlay = document.createElement("div");
			contentOverlay.className = "entry-content-overlay";

			// Move title and summary into the overlay
			const title = partner.querySelector(".entry-title");
			const summary = partner.querySelector(".entry-summary");

			if (title) contentOverlay.appendChild(title.cloneNode(true));
			if (summary) contentOverlay.appendChild(summary.cloneNode(true));

			// Hide original title and summary
			if (title) title.style.display = "none";
			if (summary) summary.style.display = "none";

			// Add overlay to the partner
			partner.appendChild(contentOverlay);
			
			// Create a full-item link that wraps the entire grid item
			if (permalink) {
				// Make the entire item clickable
				partner.style.cursor = "pointer";
				
				// Add click event to the entire partner item
				partner.addEventListener("click", function(e) {
					// Only navigate if we're on a small screen or if the click wasn't on a link
					const isSmallScreen = window.matchMedia("(max-width: 768px)").matches;
					const clickedOnLink = e.target.tagName === "A" || e.target.closest("a");
					
					if (isSmallScreen || !clickedOnLink) {
						window.location.href = permalink;
					}
				});
				
				// Add keyboard accessibility
				partner.addEventListener("keydown", function(e) {
					// Enter key
					if (e.key === "Enter") {
						window.location.href = permalink;
					}
				});
			}
		});
	}

	// Initialize when DOM is fully loaded
	document.addEventListener("DOMContentLoaded", function () {
		initNetworkPartners();
	});
})();
