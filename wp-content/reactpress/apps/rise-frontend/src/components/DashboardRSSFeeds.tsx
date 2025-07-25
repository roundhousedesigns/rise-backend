import RSSFeed from '@components/RSSFeed';

export default function DashboardRSSFeeds() {
	return (
		<RSSFeed
			feeds={[
				{
					title: 'Playbill',
					feedUrl: 'https://playbill.com/rss/news',
					fieldMap: { date: 'dc:date' },
				},
				{
					title: 'Broadway News',
					feedUrl: 'https://www.broadwaynews.com/rss/',
				},
				{
					title: 'Broadway World',
					feedUrl: 'https://www.broadwayworld.com/rss/news.xml',
				},
				{
					title: 'Variety: Theater',
					feedUrl: 'https://variety.com/v/theater/feed/',
				},
			]}
			limit={6}
		/>
	);
}
