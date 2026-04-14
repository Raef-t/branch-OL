import 'dart:async';

import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/failure_state_component.dart';
import '/features/home/presentation/view/widgets/batch_name_tooltip_widget.dart';
import '/features/home/presentation/view/widgets/shimmer_line_chart_home_view.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/core/helpers/build_line_chart_data_helper.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/features/home/presentation/managers/cubits/batch_average/batch_average_cubit.dart';
import '/features/home/presentation/managers/cubits/batch_average/batch_average_state.dart';

class CustomLineChartInHomeView extends StatefulWidget {
  const CustomLineChartInHomeView({super.key});

  @override
  State<CustomLineChartInHomeView> createState() =>
      _CustomLineChartInHomeViewState();
}

class _CustomLineChartInHomeViewState extends State<CustomLineChartInHomeView> {
  List<LineBarSpot>? _touchedSpots;
  String? _selectedBatchName;
  Timer? _tooltipTimer;

  void _showTooltip(String batchName) {
    _tooltipTimer?.cancel();
    setState(() => _selectedBatchName = batchName);
    _tooltipTimer = Timer(const Duration(seconds: 1), () {
      if (mounted) setState(() => _selectedBatchName = null);
    });
  }

  @override
  void dispose() {
    _tooltipTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.landscape;
    return BlocBuilder<BatchAverageCubit, BatchAveragesState>(
      builder: (context, state) {
        if (state is BatchAveragesSuccessState) {
          final listOfBatchAverageModel = state.listOfBatchAverageModelInCubit;
          if (listOfBatchAverageModel.isEmpty) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد تقييم للشعب',
            );
          }
          return Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              SymmetricPaddingWithChild.horizontal10(
                context: context,
                child: SizedBox(
                  width: size.width * 0.86,
                  height: size.height * (isRotait ? 0.25 : 0.13),
                  child: LineChart(
                    buildLineChartDataHelper(
                      context: context,
                      listOfBatchAverageModel: listOfBatchAverageModel,
                      touchCallback: (event, response) {
                        if (event is FlTapUpEvent) {
                          setState(() {
                            _touchedSpots = response?.lineBarSpots;
                          });
                        }
                      },
                      showingTooltipIndicators: _touchedSpots != null
                          ? [ShowingTooltipIndicators(_touchedSpots!)]
                          : [],
                      onBatchNameTap: _showTooltip,
                    ),
                  ),
                ),
              ),
              if (_selectedBatchName != null)
                BatchNameTooltipWidget(batchName: _selectedBatchName!),
            ],
          );
        } else if (state is BatchAveragesFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<BatchAverageCubit>().getBatchAverages(),
          );
        } else {
          return const ShimmerLineChartHomeView();
        }
      },
    );
  }
}
