@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h3 class="fw-bold mb-1">Dashboard</h3>
                <p class="text-muted mb-0">Ringkasan performa bisnis Anda</p>
              </div>
              <div class="d-flex gap-2">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary">
                  <i class="ti ti-shopping-cart me-2"></i>Transaksi
                </a>
                <a href="{{ route('detail_product.index') }}" class="btn btn-outline-success">
                  <i class="ti ti-package me-2"></i>Produk
                </a>
              </div>
            </div>
          </div>
        </div>

        <!--  Row 1: Quick Access Menu + Sales Scorecard -->
        <div class="row mb-4">
          <div class="col-lg-8">
            <!-- Quick Access Menu -->
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4 d-flex align-items-center">
                  <i class="ti ti-bolt me-2 text-warning fs-5"></i>Akses Cepat
                </h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <a href="{{ route('transactions.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-lift">
                      <div class="card-body p-3 text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                          <i class="ti ti-shopping-cart fs-3 text-primary"></i>
                        </div>
                        <h6 class="fw-semibold mb-1">Transaksi</h6>
                        <small class="text-muted">Kelola penjualan</small>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-6">
                    <a href="{{ route('detail_product.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-lift">
                      <div class="card-body p-3 text-center">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                          <i class="ti ti-package fs-3 text-success"></i>
                        </div>
                        <h6 class="fw-semibold mb-1">Detail Produk</h6>
                        <small class="text-muted">Kelola produk</small>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-6">
                    <a href="{{ route('raw_stock.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-lift">
                      <div class="card-body p-3 text-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                          <i class="ti ti-package fs-3 text-warning"></i>
                        </div>
                        <h6 class="fw-semibold mb-1">Bahan Baku</h6>
                        <small class="text-muted">Kelola stok</small>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-6">
                    <a href="{{ route('qc_check.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-lift">
                      <div class="card-body p-3 text-center">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                          <i class="ti ti-checklist fs-3 text-info"></i>
                        </div>
                        <h6 class="fw-semibold mb-1">Quality Control</h6>
                        <small class="text-muted">Cek kualitas</small>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sales Scorecard -->
          <div class="col-lg-4">
            <div class="card overflow-hidden border-0 shadow-sm">
              <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="card-title fw-semibold mb-0">
                    <i class="ti ti-currency-dollar me-2 text-success"></i>Total Penjualan
                  </h5>
                  <div class="bg-success bg-opacity-10 rounded-circle p-2">
                    <i class="ti ti-chart-line text-success"></i>
                  </div>
                </div>
                <h2 class="fw-bold text-success mb-3">{{ $totalSales['formatted'] }}</h2>
                <div class="d-flex align-items-center gap-2 mb-3">
                  <span class="badge bg-success-subtle text-success px-3 py-2">
                    <i class="ti ti-receipt"></i> {{ $totalSales['count'] }} Transaksi
                  </span>
                  <span class="badge bg-success-subtle text-success px-3 py-2">
                    <i class="ti ti-package fs-3 text-success"></i> {{ $totalQuantityProductSold['formatted'] }}
                  </span>
                </div>                
                <p class="text-muted mb-0 small">
                  Penjualan dari seluruh transaksi hingga hari ini
                </p>
                
              </div>
            </div>
          </div>
        </div>

        {{-- Tracking New Order Status --}}

        <!--  Row 2: Monthly Bar Chart + Best-Selling Products Pie Chart -->
        <div class="row">
          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm">
              <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <h5 class="card-title fw-semibold mb-1">
                      <i class="ti ti-chart-bar me-2 text-primary"></i>Performa Penjualan per Bulan
                    </h5>
                    <small class="text-muted">Data transaksi berdasarkan tahun dan bulan</small>
                  </div>
                </div>
              </div>
              <div class="card-body p-4">
                <div id="monthlyChart" style="height: 350px;"></div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm">
              <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-semibold mb-1">
                  <i class="ti ti-pie-chart me-2 text-info"></i>Produk Terlaris
                </h5>
                <small class="text-muted">Distribusi berdasarkan total penjualan</small>
              </div>
              <div class="card-body p-4">
                <div id="productionChart" style="height: 350px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!--  Row 3: Raw Stock Table + Transaction Timeline -->
        <div class="row mt-4">
          <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm">
              <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-semibold mb-0">
                  <i class="ti ti-inbox me-2 text-warning"></i>Bahan Baku Terbaru
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="table-responsive">
                  <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                      <tr>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Material</h6>
                        </th>
                        <th class="border-bottom-0 text-end">
                          <h6 class="fw-semibold mb-0">Qty</h6>
                        </th>
                        <th class="border-bottom-0 text-end">
                          <h6 class="fw-semibold mb-0">Harga Satuan</h6>
                        </th>
                        {{-- <th class="border-bottom-0 text-end">
                          <h6 class="fw-semibold mb-0">Total Harga</h6>
                        </th>                         --}}
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($latestRawStocks as $stock)
                      <tr>
                        <td class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">{{ $stock['name'] }}</h6>
                        </td>
                        <td class="border-bottom-0 text-end">
                          <p class="mb-0 fw-normal">{{ $stock['qty'] }}</p>
                        </td>
                        <td class="border-bottom-0 text-end">
                          <p class="mb-0 fw-normal">{{ $stock['formatted_price'] }}</p>
                        </td>
                        {{-- <td class="border-bottom-0 text-end">
                          <h6 class="fw-semibold mb-0 fs-4">{{ $stock['formatted_total'] }}</h6>
                        </td> --}}
                      </tr>
                      @empty
                      <tr>
                        <td colspan="5" class="text-center text-muted py-3">Tidak ada data pembelian</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm">
              <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-semibold mb-0">
                  <i class="ti ti-timeline me-2 text-danger"></i>Transaksi Terbaru
                </h5>
              </div>
              <div class="card-body p-4">
                <ul class="timeline-widget mb-0 position-relative">
                  @forelse($latestTransactions as $trans)
                  <li class="timeline-item d-flex position-relative overflow-hidden pb-3">
                    <div class="timeline-time text-dark flex-shrink-0 text-end me-3 fw-semibold" style="min-width: 70px;">
                      {{ $trans['time'] }}
                    </div>
                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                      <span class="timeline-badge border-2 border border-{{ $trans['status_color'] }} flex-shrink-0 my-2" style="width: 14px; height: 14px;"></span>
                    </div>
                    <div class="timeline-desc fs-3 text-dark ms-3">
                      <div class="fw-semibold">Transaksi #{{ $trans['id'] }}</div>
                      <small class="text-muted d-block">{{ $trans['date'] }} • {{ $trans['method'] }}</small>
                      <div class="mt-2">
                        <span class="badge bg-{{ $trans['status_color'] }}-subtle text-{{ $trans['status_color'] }} me-2">
                          {{ $trans['status'] }}
                        </span>
                        <span class="fw-bold text-primary">{{ $trans['total'] }}</span>
                      </div>
                    </div>
                  </li>
                  @empty
                  <li class="text-center text-muted py-4">
                    <i class="ti ti-inbox-off fs-1 d-block mb-2"></i>
                    Tidak ada transaksi
                  </li>
                  @endforelse
                </ul>
              </div>
            </div>
          </div>
        </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Bar Chart with Year Legend
    const monthlyData = @json($monthlyData);
    const monthlyCategories = monthlyData.categories || [];
    const monthlySeries = monthlyData.series || [];
    const years = monthlyData.years || [];

    // Generate colors for each year
    const colorPalette = ['#0d6efd', '#198754', '#ffc107', '#fd7e14', '#6f42c1', '#e83e8c', '#20c997', '#dc3545'];
    
    // Handle empty data
    if (monthlySeries.length === 0) {
        monthlySeries.push({
            name: 'No Data',
            data: new Array(12).fill(0)
        });
    }
    
    const monthlyChartOptions = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            }
        },
        colors: colorPalette.slice(0, monthlySeries.length),
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: monthlyCategories,
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Total Penjualan (Rp)',
                style: {
                    fontSize: '12px'
                }
            },
            labels: {
                formatter: function (value) {
                    if (value >= 1000000) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                    }
                    return 'Rp ' + value;
                },
                style: {
                    fontSize: '11px'
                }
            }
        },
        tooltip: {
            enabled: true,
            y: {
                formatter: function (value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                }
            },
            shared: true,
            intersect: false
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetY: -5,
            fontSize: '13px',
            markers: {
                width: 12,
                height: 12,
                radius: 6
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5
            }
        },
        grid: {
            borderColor: '#e7e7e7',
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        series: monthlySeries.map((series, index) => ({
            name: series.name,
            data: series.data
        }))
    };

    const monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), monthlyChartOptions);
    monthlyChart.render();

    // Best-Selling Products Pie Chart
    const productionData = @json($productionDistribution);
    const pieLabels = productionData.length > 0 ? productionData.map(item => item.label) : ['No Data'];
    const pieSales = productionData.length > 0 ? productionData.map(item => item.sales) : [1];

    // Calculate percentages for display
    const totalSales = pieSales.reduce((sum, val) => sum + val, 0);
    const piePercentages = pieSales.map(sales => totalSales > 0 ? (sales / totalSales * 100) : 0);

    // Extended color palette to support top 5 + Others
    const extendedColorPalette = ['#0d6efd', '#198754', '#ffc107', '#fd7e14', '#6f42c1', '#8b5cf6'];

    const pieChartOptions = {
        chart: {
            type: 'pie',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true
                }
            }
        },
        labels: pieLabels,
        colors: extendedColorPalette.slice(0, pieLabels.length),
        series: pieSales,
        legend: {
            position: 'bottom',
            fontSize: '12px',
            formatter: function(seriesName, opts) {
                const value = pieSales[opts.seriesIndex];
                const percentage = piePercentages[opts.seriesIndex];
                return seriesName + ': Rp ' + new Intl.NumberFormat('id-ID').format(value) + ' (' + percentage.toFixed(1) + '%)';
            },
            itemMargin: {
                horizontal: 5,
                vertical: 3
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '60%'
                },
                expandOnClick: true
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                const value = pieSales[opts.seriesIndex];
                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            },
            style: {
                fontSize: '11px',
                fontWeight: 600
            },
            dropShadow: {
                enabled: true,
                top: 1,
                left: 1,
                blur: 1,
                opacity: 0.8
            }
        },
        tooltip: {
            enabled: true,
            y: {
                formatter: function (value, { seriesIndex }) {
                    const percentage = piePercentages[seriesIndex];
                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value) + ' (' + percentage.toFixed(2) + '%)';
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    position: 'bottom',
                    fontSize: '10px'
                }
            }
        }]
    };

    const pieChart = new ApexCharts(document.querySelector("#productionChart"), pieChartOptions);
    pieChart.render();
});
</script>
<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endpush
