@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
        <!--  Row 1: Quick Access Menu + Sales Scorecard -->
        <div class="row mb-4">
          <div class="col-lg-8">
            <!-- Quick Access Menu -->
            <div class="card border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4">
                  <i class="ti ti-bolt me-2 text-warning"></i>Quick Access
                </h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                      <i class="ti ti-shopping-cart fs-5"></i>
                      <span>Transaksi</span>
                    </a>
                  </div>
                  <div class="col-md-6">
                    <a href="{{ route('detail_product.index') }}" class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                      <i class="ti ti-package fs-5"></i>
                      <span>Detail Produk</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sales Scorecard -->
          <div class="col-lg-4">
            <div class="card overflow-hidden border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-3">
                  <i class="ti ti-currency-dollar me-2 text-success"></i>Total Penjualan
                </h5>
                <h2 class="fw-bold text-success mb-3">{{ $totalSales['formatted'] }}</h2>
                <div class="d-flex align-items-center gap-2 mb-3">
                  <span class="badge bg-success-subtle text-success px-3 py-2">
                    <i class="ti ti-arrow-up"></i> {{ $totalSales['count'] }} Transaksi
                  </span>
                </div>
                <p class="text-muted mb-0 fs-3">
                  Total nilai penjualan dari seluruh transaksi hingga hari ini
                </p>
              </div>
            </div>
          </div>
        </div>

        <!--  Row 2: Monthly Bar Chart + Production Pie Chart -->
        <div class="row">
          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm">
              <div class="card-body p-4">
                <div class="mb-4">
                  <h5 class="card-title fw-semibold">
                    <i class="ti ti-chart-bar me-2 text-primary"></i>Performa Penjualan per Bulan
                  </h5>
                  <small class="text-muted">Data transaksi 12 bulan terakhir</small>
                </div>
                <div id="monthlyChart" style="height: 300px;"></div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4">
                  <i class="ti ti-pie-chart me-2 text-info"></i>Distribusi Produk
                </h5>
                <div id="productionChart" style="height: 300px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!--  Row 3: Raw Stock Table + Transaction Timeline -->
        <div class="row mt-4">
          <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4">
                  <i class="ti ti-inbox me-2 text-warning"></i>Bahan Baku Terbaru
                </h5>
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
                        <th class="border-bottom-0 text-end">
                          <h6 class="fw-semibold mb-0">Total Harga</h6>
                        </th>
                        <th class="border-bottom-0 text-center">
                          <h6 class="fw-semibold mb-0">Tanggal</h6>
                        </th>
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
                        <td class="border-bottom-0 text-end">
                          <h6 class="fw-semibold mb-0 fs-4">{{ $stock['formatted_total'] }}</h6>
                        </td>
                        <td class="border-bottom-0 text-center">
                          <small class="text-muted">{{ $stock['date'] }}</small>
                        </td>
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
              <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4">
                  <i class="ti ti-timeline me-2 text-danger"></i>Transaksi Terbaru
                </h5>
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
    // Monthly Bar Chart
    const monthlyData = @json($monthlyData);
    const monthlyLabels = monthlyData.map(item => item.month);
    const monthlySales = monthlyData.map(item => item.total);

    const monthlyChartOptions = {
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        colors: ['#0d6efd'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%',
                endingShape: 'rounded'
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
            categories: monthlyLabels,
        },
        yaxis: {
            title: {
                text: 'Rp (Rupiah)'
            },
            min: 0,
            max: 500000,
            tickAmount: 5,
            labels: {
                formatter: function (value) {
                    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
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
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        },
        series: [{
            name: 'Penjualan',
            data: monthlySales
        }]
    };

    const monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), monthlyChartOptions);
    monthlyChart.render();

    // Production Pie Chart
    const productionData = @json($productionDistribution);
    const pieLabels = productionData.map(item => item.label);
    const pieCounts = productionData.map(item => item.count);

    const pieChartOptions = {
        chart: {
            type: 'pie',
            height: 300
        },
        labels: pieLabels,
        colors: ['#0d6efd', '#198754', '#ffc107', '#fd7e14', '#6f42c1', '#e83e8c'],
        series: pieCounts,
        legend: {
            position: 'bottom',
            fontSize: '12px'
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    offset: -5
                }
            }
        },
        dataLabels: {
            formatter: function (val) {
                return Math.round(val) + '%';
            }
        }
    };

    const pieChart = new ApexCharts(document.querySelector("#productionChart"), pieChartOptions);
    pieChart.render();
});
</script>
@endpush
