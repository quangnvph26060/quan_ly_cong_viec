<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                @if (auth('admin')->user()->level === 2)
                    <li class="menu-title" data-key="t-menu">Menu</li>
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i data-feather="home"></i>
                            <span data-key="t-dashboard">Dashboard</span>
                        </a>
                    </li>
           
                <li class="menu-title mt-2" data-key="t-components">Quản lý</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="shopping-bag"></i>
                        <span data-key="t-apps">Quản lý dự án</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.project.add') }}">
                                <span data-key="t-calendar">Thêm mới</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.project.list') }}">
                                <span data-key="t-chat">Danh sách</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="shopping-bag"></i>
                        <span data-key="t-apps">Quản lý công việc</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        {{-- <li>
                            <a href="{{ route("admin.mission.add") }}">
                                <span data-key="t-calendar">Thêm mới</span>
                            </a>
                        </li> --}}
                        <li>
                            <a href="{{ route('admin.mission.list') }}">
                                <span data-key="t-chat">Danh sách</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if (auth('admin')->user()->level === 2)
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý nhân viên</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('admin.user.add') }}">
                                    <span data-key="t-calendar">Thêm mới</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user.list') }}">
                                    <span data-key="t-chat">Danh sách</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý admin</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('admin.admin.add') }}">
                                    <span data-key="t-calendar">Thêm mới</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.admin.list') }}">
                                    <span data-key="t-chat">Danh sách</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý thông báo</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('admin.notification.add') }}">
                                    <span data-key="t-calendar">Thêm mới</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.notification.list') }}">
                                    <span data-key="t-chat">Danh sách</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Cài đặt KPI</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('admin.setting-kpi.add') }}">
                                    <span data-key="t-calendar">Thêm mới</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.setting-kpi.list') }}">
                                    <span data-key="t-chat">Danh sách</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Tin nội bộ</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('admin.news.add') }}">
                                    <span data-key="t-calendar">Thêm mới</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.news.list') }}">
                                    <span data-key="t-chat">Danh sách</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- <li>
                    <a href="{{ route('admin.salary.list') }}">
                        <i data-feather="shopping-bag"></i>
                        <span data-key="t-apps">Bảng lương</span>
                    </a>
                    </li> --}}
                    <li>
                        <a href="{{ route('admin.overview') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Tổng quan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.fanpage.list') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý Seeding</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.check-in.index') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Chấm công</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.check-in.payroll') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Bảng lương</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.receipt.index') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý phiếu thu</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.paymentslip.index') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý phiếu chi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.client.index') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý khách hàng</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.bill.index') }}">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý báo giá</span>
                        </a>
                    </li>
                @endif
                @if (in_array(auth('admin')->user()->level, [2, 3]))
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Quản lý hoá đơn</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('admin.invoice.index') }}">
                                    <span data-key="t-calendar">Hoá đơn mua vào</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.invoice.indexSalesInvoice') }}">
                                    <span data-key="t-chat">Hoá đơn bán vào</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                    {{-- <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="shopping-bag"></i>
                        <span data-key="t-apps">Cấu hình</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.zalo.oa.index') }}">
                                <span data-key="t-calendar">Cấu hình OA/ZNS</span>
                            </a>
                        </li>
                    </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="shopping-bag"></i>
                            <span data-key="t-apps">Truy vấn ZNS</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('admin.zalo.message.znsmessage') }}">
                                    <span data-key="t-calendar">Tin nhắn ZNS</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.zalo.message.znsQuota') }}">
                                    <span data-key="t-calendar">Hạn mức ZNS</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.zalo.template.znsTemplate') }}">
                                    <span data-key="t-calendar">Template ZNS</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-title mt-2" data-key="t-components">Cài đặt</li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="users"></i>
                            <span data-key="t-pages">Quyền</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{route('admin.role.list')}}" data-key="t-starter-page">Thêm quyền</a></li>
                            <li><a href="{{route('admin.role.list')}}" data-key="t-starter-page">Danh sách</a></li>
                            <li><a href="{{route('admin.permission.list')}}" data-key="t-maintenance">Quyền truy cập</a></li>
                        </ul>
                    </li> --}}
              
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
