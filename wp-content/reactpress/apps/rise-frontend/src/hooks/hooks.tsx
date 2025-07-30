import { SearchContext } from '@context/SearchContext';
import { RSSFeedState, RSSPostFieldMap } from '@lib/types';
import {
	parseRSSItems,
	searchFilterSetsAreEqual,
	validateEmail,
	validatePassword,
	validateProfileSlug,
} from '@lib/utils';
import useUserProfile from '@queries/useUserProfile';
import { useContext, useEffect, useRef, useState } from 'react';

/**
 * Custom hooks.
 */

/**
 * Use Local Storage Hook.
 *
 * Works the same as useState, but using localStorage.
 *
 * @see {@link https://github.com/mikejolley/morrics-magical-cauldron/blob/main/src/hooks/use-local-storage.js}
 *
 * @param {string} key The key to set in localStorage for this value
 * @param {Object} defaultValue The value to use if it is not already in localStorage
 * @param {{serialize: Function, deserialize: Function}} options The serialize and deserialize functions to use (defaults to JSON.stringify and JSON.parse respectively)
 */
export const useLocalStorage = (
	key: string,
	defaultValue: any = '',
	{
		serialize = JSON.stringify,
		deserialize = JSON.parse,
		expiresIn = 0, // Time in milliseconds, 0 means no expiration
	}: {
		serialize?: (val: any) => string;
		deserialize?: (val: string) => any;
		expiresIn?: number;
	} = {}
) => {
	const [state, setState] = useState<any>(() => {
		const valueInLocalStorage = window.localStorage.getItem(key);
		if (valueInLocalStorage) {
			try {
				const { value, timestamp } = deserialize(valueInLocalStorage);
				if (expiresIn && Date.now() - timestamp > expiresIn) {
					window.localStorage.removeItem(key);
					return typeof defaultValue === 'function' ? defaultValue() : defaultValue;
				}
				return value;
			} catch (e) {
				// If deserialization fails, return default value
				return typeof defaultValue === 'function' ? defaultValue() : defaultValue;
			}
		}
		return typeof defaultValue === 'function' ? defaultValue() : defaultValue;
	});

	const prevKeyRef = useRef(key);

	useEffect(() => {
		const prevKey = prevKeyRef.current;
		if (prevKey !== key) {
			window.localStorage.removeItem(prevKey);
		}
		prevKeyRef.current = key;
		const serializedValue = serialize({
			value: state,
			timestamp: Date.now(),
		});
		window.localStorage.setItem(key, serializedValue);
	}, [key, state, serialize]);

	return [state, setState];
};

/**
 * Format an error message based on the error code.
 *
 * @param {string} errorCode The error message returned by the server.
 * @param {string} defaultMessage The default message to use if no specific case matches.
 * @returns {string} The formatted error message.
 */
export const useErrorMessage = (errorCode?: string, defaultMessage: string = 'Error'): string => {
	// TODO Return an object keyed by code to handle multiple errors at once.
	if (!errorCode) return '';

	switch (errorCode) {
		// Login errors
		case 'invalid_username':
		case 'invalid_email':
			return 'No account exists for that email address.';
		case 'empty_login':
			return 'Please enter a username or email address.';
		case 'invalid_account':
			return 'Please use a different account.';
		case 'bad_login':
			return 'Something went wrong. Please try again.';

		// Password Errors
		case 'empty_password':
			return 'Please enter your password.';
		case 'incorrect_password':
			return 'Incorrect password.';
		case 'password_mismatch':
			return 'Passwords do not match.';
		case 'password_too_weak':
			return 'Please make sure your password contains at least one lowercase letter, one uppercase letter, one number, and one special character.';

		// Registration errors
		case 'existing_user_login':
			return 'An account already exists for that email address. Please try logging in.';
		case 'unspecified_create_user_error':
			return 'Something went wrong. Please try again.';

		// Change profile slug errors
		case 'user_not_found':
			return 'There was an error updating your profile URL. Please contact support.';
		case 'user_not_authorized':
			return 'You do not appear to be logged in.';
		case 'user_slug_not_unique':
			return 'This alias is already in use. Please choose another.';
		case 'user_slug_invalid':
			return 'Only letters, numbers, dashes (-) and underscores (_) are allowed.';

		// Profile Edit errors
		case 'conflict_range_overlap':
			return 'This date range overlaps with an existing busy time. Please try again.';
		case 'multilingual_no_languages':
			return 'Please enter at least one language.';

		default:
			return defaultMessage + ': ' + errorCode;
	}
};

/**
 * Get the URL for a user profile.
 *
 * @param {slug} The user profile slug.
 * @returns The user profile URL.
 */
export const useProfileUrl = (slug: string): string => {
	return slug ? `profile/${slug}` : '';
};

/**
 * Validate a user profile slug.
 *
 * @param {slug} The user profile slug.
 * @return True if the slug is valid.
 */
export const useValidateProfileSlug = (slug: string): boolean => validateProfileSlug(slug);

/**
 * Validate a password to meet requirements
 *
 * @param {password} The password to validate
 * @return string|undefined 'weak' or 'strong'
 */
export const useValidatePassword = (password: string): string | undefined =>
	validatePassword(password);

/**
 * Validate an email address.
 *
 * @param {email} The email address.
 * @return True if the email address is valid.
 */
export const useValidateEmail = (email: string): boolean => validateEmail(email);

/**
 * Returns a boolean indicating whether the currently restored Saved Search has been changed
 * by the user.
 *
 * @return boolean True if the filter set has changed.
 */
export const useSavedSearchFiltersChanged = (): boolean => {
	const {
		search: {
			filters: { filterSet: currentFilterSet },
			savedSearch: { filterSet: savedSearchFilterSet },
		},
	} = useContext(SearchContext);

	return !searchFilterSetsAreEqual(currentFilterSet, savedSearchFilterSet);
};

/**
 * Calculates the completion percentage of a user's profile.
 *
 * @param {number} profileId - The ID of the user's profile to calculate completion for.
 * @return {number} The completion percentage of the user's profile, as a whole number between 0 and 100.
 */
export const useProfileCompletion = (profileId: number | null): number => {
	const [profile] = useUserProfile(profileId);

	// Field weights
	const fieldsToCalculate = {
		selfTitle: 10,
		email: 10,
		image: 10,
		homebase: 10,
		pronouns: 5,
		description: 10,
		resume: 15,
		education: 5,
		locations: 10,
		socials: 5,
		website: 5,
		unions: 5,
		experienceLevels: 5,
		credits: 30,
		// firstName: 1,
		// lastName: 1,
		// phone: 1,
		// willTravel: 1,
		// willTour: 1,
		// partnerDirectories: 1,
		// multilingual: 1,
		// languages: 1,
		// genderIdentities: 1,
		// racialIdentities,
		// personalIdentities,
		// mediaVideo1: 1,
		// mediaVideo2: 1,
		// mediaImage1: 1,
		// mediaImage2: 1,
		// mediaImage3: 1,
		// mediaImage4: 1,
		// mediaImage5: 1,
		// mediaImage6: 1,
	};

	// Add up the total weights for each field
	const totalWeight = Object.values(fieldsToCalculate).reduce((a, b) => a + b, 0);

	// Calculate the profile completion percentage. If a field's value is truthy, add the weight. If a field's value is an object, only add the weight if at least one of its properties is truthy.
	let profileCompletion = 0;

	if (!profile) {
		return 0;
	}

	// Calculate the weight.
	for (const [field, weight] of Object.entries(fieldsToCalculate)) {
		if (profile[field] && typeof profile[field] === 'object') {
			if (Object.values(profile[field]).some((value) => value)) {
				profileCompletion += weight;
			}
		} else if (profile[field]) {
			profileCompletion += weight;
		}
	}

	// Divide the score by the total weight, multiplied by 100, to get the profile completion percentage as a decimal. Round to the nearest integer.
	return Math.round((profileCompletion / totalWeight) * 100);
};

/**
 * Use RSS Feed Hook with improved reliability and caching.
 *
 * @param {string} feedUrl - The URL of the RSS feed.
 * @param {RSSPostFieldMap} fieldMap - The field map to use for parsing.
 */
export const useRSSFeed = (feedUrl: string, fieldMap?: RSSPostFieldMap): RSSFeedState => {
	// Cache configuration
	const CACHE_DURATION = 20 * 60 * 1000; // 20 minutes in milliseconds
	const cacheKey = `rss_feed_${btoa(feedUrl).replace(/[^a-zA-Z0-9]/g, '')}`; // Safe cache key
	
	// Multiple CORS proxy services for fallback
	const CORS_PROXIES = [
		'https://api.allorigins.win/raw?url=',
		'https://api.codetabs.com/v1/proxy?quest=',
		'https://corsproxy.io/?',
		'https://api.allorigins.win/get?url=', // Alternative allorigins endpoint
	];

	const [state, setState] = useState<RSSFeedState>(() => {
		// Check for cached data on initialization
		try {
			const cached = localStorage.getItem(cacheKey);
			if (cached) {
				const { data, timestamp } = JSON.parse(cached);
				const isExpired = Date.now() - timestamp > CACHE_DURATION;
				
				if (!isExpired && data.posts && data.posts.length > 0) {
					console.log(`📦 Using cached RSS data: ${feedUrl} (${data.posts.length} posts)`);
					return {
						posts: data.posts,
						loading: false,
						error: null,
					};
				} else if (isExpired) {
					// Remove expired cache
					localStorage.removeItem(cacheKey);
				}
			}
		} catch (e) {
			// If cache is corrupted, remove it
			localStorage.removeItem(cacheKey);
		}
		
		// No valid cache found, start with loading state
		return {
			posts: [],
			loading: true,
			error: null,
		};
	});

	useEffect(() => {
		let isCancelled = false;

		const fetchFeedWithRetry = async (retryCount = 0, proxyIndex = 0): Promise<void> => {
			if (isCancelled) return;

			try {
				setState((prev) => ({ ...prev, loading: true, error: null }));

				const proxy = CORS_PROXIES[proxyIndex];
				const response = await fetch(proxy + encodeURIComponent(feedUrl), {
					method: 'GET',
					headers: {
						Accept: 'application/rss+xml, application/xml, text/xml',
						'User-Agent': 'Mozilla/5.0 (compatible; RSS Reader)',
					},
				});

				if (!response.ok) {
					throw new Error(`HTTP ${response.status}: ${response.statusText}`);
				}

						let xmlText = await response.text();

		// Handle JSON responses from some proxies (like api.allorigins.win/get)
		if (proxy.includes('api.allorigins.win/get')) {
			try {
				const jsonData = JSON.parse(xmlText);
				xmlText = jsonData.contents || jsonData.data || xmlText;
			} catch (e) {
				// If it's not JSON, use the text as-is
			}
		}

		// Optional debugging (uncomment for troubleshooting)
		// console.log('RSS Feed Debug Info:', {
		// 	url: feedUrl,
		// 	proxy: proxy,
		// 	contentLength: xmlText.length,
		// 	contentPreview: xmlText.substring(0, 200),
		// });

		// More flexible validation - check if XML content exists anywhere in the response
		const trimmedContent = xmlText.trim();
		const xmlStartIndex = Math.max(
			trimmedContent.indexOf('<?xml'),
			trimmedContent.indexOf('<rss'),
			trimmedContent.indexOf('<feed')
		);

		let actualXmlContent = xmlText;
		
		// If XML doesn't start at the beginning, try to extract it
		if (xmlStartIndex > 0) {
			actualXmlContent = trimmedContent.substring(xmlStartIndex);
		} else if (xmlStartIndex === -1) {
			// No XML found at all
			throw new Error(`No valid RSS/XML content found. Content preview: ${trimmedContent.substring(0, 200)}`);
		}

						const parser = new DOMParser();
		const xmlDoc = parser.parseFromString(actualXmlContent, 'text/xml');

				// Check for XML parsing errors
				const parseError = xmlDoc.getElementsByTagName('parsererror');
				if (parseError.length > 0) {
					throw new Error('XML parsing failed: ' + parseError[0].textContent);
				}

				const parsedPosts = parseRSSItems(xmlDoc, fieldMap);

				if (!isCancelled) {
					console.log(`✅ RSS Feed loaded: ${feedUrl} (${parsedPosts.length} posts)`);
					
					// Cache the successful result
					try {
						const cacheData = {
							data: { posts: parsedPosts },
							timestamp: Date.now(),
						};
						localStorage.setItem(cacheKey, JSON.stringify(cacheData));
						console.log(`💾 Cached RSS data: ${feedUrl}`);
					} catch (e) {
						console.warn('Failed to cache RSS data:', e);
					}
					
					setState({
						posts: parsedPosts,
						loading: false,
						error: null,
					});
				}
			} catch (err) {
				// Log errors only in development
				if (process.env.NODE_ENV === 'development') {
					console.error(`RSS feed error (attempt ${retryCount + 1}, proxy ${proxyIndex + 1}):`, err);
				}

				if (isCancelled) return;

				// Try next proxy if available
				if (proxyIndex < CORS_PROXIES.length - 1) {
					return fetchFeedWithRetry(retryCount, proxyIndex + 1);
				}

				// Retry with first proxy if we haven't exceeded retry limit
				if (retryCount < 2) {
					setTimeout(() => {
						if (!isCancelled) {
							fetchFeedWithRetry(retryCount + 1, 0);
						}
					}, Math.min(Math.pow(2, retryCount) * 500, 2000)); // Faster backoff: 0.5s, 1s, 2s max
					return;
				}

				// All retries and proxies failed
				const errorMessage = `Failed to load RSS feed: ${err instanceof Error ? err.message : 'Unknown error'}`;
				console.error(`❌ RSS Feed failed after all retries: ${feedUrl}`);
				console.error(`   Error: ${errorMessage}`);
				
				setState({
					posts: [],
					loading: false,
					error: errorMessage,
				});
			}
		};

		// Only fetch if we don't already have cached data
		if (state.posts.length === 0 && !state.error) {
			fetchFeedWithRetry();
		}

		// Cleanup function to cancel ongoing requests
		return () => {
			isCancelled = true;
		};
	}, [feedUrl, fieldMap, cacheKey]); // Added cacheKey to dependencies

	return state;
};

/**
 * Clear RSS feed cache for a specific URL or all RSS caches.
 * 
 * @param feedUrl Optional specific feed URL to clear. If not provided, clears all RSS caches.
 */
export const clearRSSCache = (feedUrl?: string): void => {
	if (feedUrl) {
		// Clear specific feed cache
		const cacheKey = `rss_feed_${btoa(feedUrl).replace(/[^a-zA-Z0-9]/g, '')}`;
		localStorage.removeItem(cacheKey);
		console.log(`🗑️ Cleared RSS cache for: ${feedUrl}`);
	} else {
		// Clear all RSS caches
		const keysToRemove: string[] = [];
		for (let i = 0; i < localStorage.length; i++) {
			const key = localStorage.key(i);
			if (key && key.startsWith('rss_feed_')) {
				keysToRemove.push(key);
			}
		}
		keysToRemove.forEach(key => localStorage.removeItem(key));
		console.log(`🗑️ Cleared ${keysToRemove.length} RSS cache entries`);
	}
};

/**
 * Stringify a state object. Useful for comparing state objects in useEffect dependencies.
 *
 * @param state The state object to stringify.
 */
export const useStringified = (state: any): string => {
	return JSON.stringify(state);
};
