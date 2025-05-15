# Changelog

All notable changes to this project will be documented in this file, starting
with 1.1.1.

## \[1.2-dev] - Unreleased

- Improve: Profile layout shifts
- Add: Notifications!
  - Badge count for displaying if any starred profiles have been updated.
  - On update, if an identical notification is found, only the newest is kept.
- Improve: New Dashboard!
- Improve: Clarify Edit Profile Sidebar (`Sidebar` -> `EditProfileSidebar`) to avoid confusion over new `<Sidebar>` layout component.
- Change: Change <BrowserRouter> to <HashRouter> for better compatibility in WP app container.
- Fix: "$lastCredits" as count var in `useUserProfile`.
- Remove: <Page> component and slug route. Static pages handled by WP theme.

## \[1.1.10] - 2024-10-14

- Add: Delete account mutation.
- Improve: Gave the changelog a proper home.
- Improve: exit -> wp_die().
- Improve: Move Rise_User::generate_default_user_slug() to standalone function rise_generate_default_user_slug().

## \[1.1.5]

- SearchFilterSetRaw -> QueryableSearchFilterSet

## \[1.1.4]

- Conflict/Job Dates
- Cron jobs

## \[1.1.3]

- changeEmail mutation.

## \[1.1.2]

- Relate 'associate' and 'assistant' terms for more holistic searching.

## \[1.1.1]

- Minor update to function naming.
- Finally started this changelog.
