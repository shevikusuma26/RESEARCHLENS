<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ResearchLens</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-900 text-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 border-r border-slate-700 overflow-y-auto">
            <div class="p-6 border-b border-slate-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-microscope text-blue-500 text-2xl"></i>
                    <span class="font-bold text-lg">ResearchLens</span>
                </div>
            </div>

            <nav class="p-4 space-y-2">
                <a href="#" class="flex items-center gap-3 px-4 py-2 bg-blue-600/20 border-l-2 border-blue-500 text-blue-400 rounded">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded transition">
                    <i class="fas fa-files"></i>
                    <span>My Projects</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded transition">
                    <i class="fas fa-brain"></i>
                    <span>Similarity Analysis</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded transition">
                    <i class="fas fa-lightbulb"></i>
                    <span>Recommendations</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded transition">
                    <i class="fas fa-trend-up"></i>
                    <span>Trending Topics</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded transition">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700 w-64 bg-slate-800">
                <div class="bg-slate-700/50 rounded-lg p-4 mb-4 text-sm">
                    <p class="font-semibold mb-1">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Top Bar -->
            <div class="bg-slate-800 border-b border-slate-700 px-8 py-4 flex justify-between items-center sticky top-0">
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <div class="flex items-center gap-4">
                    <button class="text-gray-400 hover:text-white">
                        <i class="fas fa-bell text-xl"></i>
                    </button>
                    <button class="text-gray-400 hover:text-white">
                        <i class="fas fa-sun text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-8">
                <!-- Welcome Section -->
                <div class="bg-gradient-to-r from-blue-600/10 to-cyan-600/10 border border-blue-600/20 rounded-xl p-8 mb-8">
                    <h2 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                    <p class="text-gray-400">Here's what's happening with your research today.</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 hover:border-blue-500 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">My Projects</p>
                                <p class="text-3xl font-bold">5</p>
                            </div>
                            <div class="text-4xl text-blue-500 opacity-20">
                                <i class="fas fa-files"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 hover:border-cyan-500 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Avg Novelty Score</p>
                                <p class="text-3xl font-bold">78.5%</p>
                            </div>
                            <div class="text-4xl text-cyan-500 opacity-20">
                                <i class="fas fa-sparkles"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 hover:border-purple-500 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Avg Similarity</p>
                                <p class="text-3xl font-bold">22.3%</p>
                            </div>
                            <div class="text-4xl text-purple-500 opacity-20">
                                <i class="fas fa-brain"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 hover:border-green-500 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Recommendations</p>
                                <p class="text-3xl font-bold">12</p>
                            </div>
                            <div class="text-4xl text-green-500 opacity-20">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- Novelty Score Chart -->
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">Novelty Score Trend</h3>
                        <canvas id="noveltyChart"></canvas>
                    </div>

                    <!-- Category Distribution -->
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold mb-4">Category Distribution</h3>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Recent Projects -->
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Recent Projects</h3>
                        <a href="#" class="text-blue-400 hover:text-blue-300 text-sm">View All</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-slate-700">
                                <tr>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Title</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Category</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Novelty</th>
                                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-700 hover:bg-slate-700/30 transition">
                                    <td class="py-3 px-4">IoT-Based Smart Healthcare System</td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm">Healthcare Technology</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-green-400 font-semibold">85%</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="bg-green-500/20 text-green-400 text-xs font-semibold px-3 py-1 rounded-full">Analyzed</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-slate-700 hover:bg-slate-700/30 transition">
                                    <td class="py-3 px-4">Machine Learning in Supply Chain</td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm">Artificial Intelligence</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-orange-400 font-semibold">62%</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="bg-yellow-500/20 text-yellow-400 text-xs font-semibold px-3 py-1 rounded-full">In Progress</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <button class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-plus mr-2"></i> New Project
                    </button>
                    <button class="bg-cyan-600 hover:bg-cyan-700 px-6 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-brain mr-2"></i> Analyze Project
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Novelty Score Chart
        const noveltyCtx = document.getElementById('noveltyChart').getContext('2d');
        new Chart(noveltyCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                datasets: [{
                    label: 'Novelty Score',
                    data: [65, 72, 70, 78, 85],
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            color: '#9ca3af'
                        },
                        grid: {
                            color: '#334155'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#9ca3af'
                        },
                        grid: {
                            color: '#334155'
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['AI & ML', 'Web Dev', 'Mobile', 'Healthcare', 'Other'],
                datasets: [{
                    data: [25, 20, 15, 25, 15],
                    backgroundColor: [
                        '#3b82f6',
                        '#06b6d4',
                        '#8b5cf6',
                        '#10b981',
                        '#f59e0b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#d1d5db'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
