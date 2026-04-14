import 'package:flutter/material.dart';
import '/core/components/shimmer_loading_widget.dart';

/// Skeleton loader for the batch-average line chart.
/// Designed like a YouTube/analytics bar-chart skeleton:
///   a rounded grey card with bars of varying heights growing from the bottom.
class ShimmerLineChartHomeView extends StatelessWidget {
  const ShimmerLineChartHomeView({super.key});

  // Mirror the height ratios the real chart would show.
  static const List<double> _barHeights = [
    0.35, 0.60, 0.42, 0.85, 0.55, 0.78, 0.40, 0.65,
  ];

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    final isLandscape =
        MediaQuery.orientationOf(context) == Orientation.landscape;
    final chartH = size.height * (isLandscape ? 0.25 : 0.13);
    final innerH = chartH - 28; // subtract top+bottom padding (14 each)

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 10),
      child: ShimmerLoadingWidget(
        child: Container(
          width: size.width * 0.86,
          height: chartH,
          padding: const EdgeInsets.fromLTRB(14, 14, 14, 14),
          decoration: BoxDecoration(
            color: const Color(0xffE8E8E8),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              // Bars row — growing from the bottom
              Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: List.generate(_barHeights.length, (i) {
                  return Expanded(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 3),
                      child: Container(
                        height: innerH * _barHeights[i],
                        decoration: const BoxDecoration(
                          color: Color(0xffD0D0D0),
                          borderRadius: BorderRadius.vertical(
                            top: Radius.circular(5),
                          ),
                        ),
                      ),
                    ),
                  );
                }),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
