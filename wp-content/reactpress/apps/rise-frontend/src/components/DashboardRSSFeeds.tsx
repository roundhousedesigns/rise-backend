import { Card } from '@chakra-ui/react';
import RSSFeed from '@components/RSSFeed';

export default function DashboardRSSFeeds() {
	return (
		<Card my={0} gap={2} opacity={0.9} transition='opacity 200ms ease' _hover={{ opacity: 1 }}>
			<RSSFeed
				feeds={[
					{
						title: 'Broadway News',
						feedUrl: 'https://www.broadwaynews.com/rss/',
					},
					{
						title: 'Playbill',
						feedUrl: 'https://playbill.com/rss/news',
						fieldMap: { date: 'dc:date' },
					},
					{
						title: 'Variety: Theater',
						feedUrl: 'https://variety.com/v/theater/feed/',
					},
				]}
				limit={6}
			/>
		</Card>
	);
}
