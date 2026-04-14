import 'package:flutter/material.dart';
import '/core/components/shimmer_loading_widget.dart';

/// Skeleton loader for the exams-today card content.
/// Matches [CustomSuccessStateForExamNumbersTodayCardInHomeView]:
///   count text  +  horizontal row of circle-avatar + label skeletons
///   (Facebook-style card skeleton).
class ShimmerExamsCardHomeView extends StatelessWidget {
  const ShimmerExamsCardHomeView({super.key});

  static const int _previewCount = 4;

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    final isLandscape =
        MediaQuery.orientationOf(context) == Orientation.landscape;
    // Match the real CircleAvatar radius: height * (landscape ? 0.03 : 0.021)
    final circleRadius = size.height * (isLandscape ? 0.03 : 0.021);
    final circleDiameter = circleRadius * 2;
    final labelWidth = size.width * 0.14;

    return ShimmerLoadingWidget(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          // Exam count text placeholder  (e.g. "٣ مذاكرات")
          ShimmerBox(width: size.width * 0.22, height: 14, borderRadius: 7),
          SizedBox(height: size.height * 0.022),
          // Horizontal row of exam circle placeholders
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            physics: const NeverScrollableScrollPhysics(),
            child: Directionality(
              textDirection: TextDirection.rtl,
              child: Row(
                children: List.generate(_previewCount, (i) {
                  return Padding(
                    padding: EdgeInsets.only(
                      left: i < _previewCount - 1 ? size.width * 0.073 : 0,
                    ),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // Outer circle (littlePinkColor) + inner (russetColor)
                        ShimmerCircle(diameter: circleDiameter + 4),
                        SizedBox(height: size.height * 0.008),
                        // Subject name label
                        ShimmerBox(
                          width: labelWidth,
                          height: 10,
                          borderRadius: 5,
                        ),
                      ],
                    ),
                  );
                }),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
