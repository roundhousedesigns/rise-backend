import { ApolloClient, ApolloProvider, InMemoryCache } from '@apollo/client';
import { ChakraProvider, ColorModeScript } from '@chakra-ui/react';
import { createUploadLink } from 'apollo-upload-client';
import { StrictMode } from 'react';
import ReactDOM from 'react-dom/client';
import ReactGA from 'react-ga4';
import { HashRouter } from 'react-router-dom';

import App from '@/App';
import reportWebVitals from '@/reportWebVitals';
import WordPressStyles from '@components/WordPressStyles';
import Fonts from '@theme/Fonts';
import theme from '@theme/index';

// Env vars
const { VITE_BACKEND_URL, VITE_GA4_ID, VITE_TURNSTILE_SITE_KEY } = import.meta.env;

// Initialize Google Analytics
if (VITE_GA4_ID) ReactGA.initialize(VITE_GA4_ID);

const root = ReactDOM.createRoot(document.getElementById('root') as HTMLElement);

/**
 * Apollo client.
 */
const httpLink = createUploadLink({
	uri: VITE_BACKEND_URL,
	credentials: 'include',
});

const client = new ApolloClient({
	link: httpLink as any,
	cache: new InMemoryCache(),
});

root.render(
	<StrictMode>
		<ColorModeScript initialColorMode={theme.config.initialColorMode} />
		<HashRouter future={{ v7_startTransition: true, v7_relativeSplatPath: true }}>
			<ApolloProvider client={client}>
				<ChakraProvider resetCSS={true} theme={theme}>
					<Fonts />
					<WordPressStyles />
					<App />
				</ChakraProvider>
			</ApolloProvider>
		</HashRouter>
	</StrictMode>
);

const sendAnalytics = () => {
	ReactGA.send({ hitType: 'pageview', page: window.location.pathname });
};

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals(sendAnalytics);
