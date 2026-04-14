import 'package:flutter/material.dart';
import '/core/components/shimmer_loading_widget.dart';
import '/core/styles/colors_style.dart';

/// Skeleton loader for the exams list.
/// Shows 4 placeholder rows that mirror [CustomExamAndDividerAndTimeCardsInExamView]:
///   ┌──────────────── exam card (flex 5) ────────────────┐ │ │ time col (flex 1)
///   │  ○  [subject name ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓]             │ │ │  ▓▓▓▓▓           │
///   │     [lesson  ▓▓▓▓▓▓▓▓▓▓▓▓▓]                        │ │ │                   │
///   │     [course ▓▓▓▓▓▓▓▓] ⊙   [classroom ▓▓▓▓▓▓▓] ⊙   │ │ │  ▓▓▓▓▓           │
///   └────────────────────────────────────────────────────┘ │ │
class ShimmerExamsViewCards extends StatelessWidget {
  const ShimmerExamsViewCards({super.key});

  static const int _previewCount = 4;

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    return ShimmerLoadingWidget(
      child: Column(
        children: List.generate(_previewCount, (index) {
          return _ExamRowSkeleton(size: size, isFirst: index == 0);
        }),
      ),
    );
  }
}

class _ExamRowSkeleton extends StatelessWidget {
  const _ExamRowSkeleton({required this.size, required this.isFirst});
  final Size size;
  final bool isFirst;

  @override
  Widget build(BuildContext context) {
    return Padding(
      // left26AndRight25 → left: w*0.062, right: w*0.06
      padding: EdgeInsets.only(
        left: size.width * 0.062,
        right: size.width * 0.06,
      ),
      child: IntrinsicHeight(
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ── Exam card (flex 5) ────────────────────────────────
            Expanded(flex: 5, child: _ExamCardSkeleton(size: size)),
            // Width17
            SizedBox(width: size.width * 0.041),
            // Vertical divider
            Container(
              width: 1,
              color: ColorsStyle.veryLittleGreyColor,
            ),
            // Width10
            SizedBox(width: size.width * 0.024),
            // ── Time column (flex 1) ──────────────────────────────
            Expanded(child: _TimeColumnSkeleton(size: size, isFirst: isFirst)),
          ],
        ),
      ),
    );
  }
}

class _ExamCardSkeleton extends StatelessWidget {
  const _ExamCardSkeleton({required this.size});
  final Size size;

  @override
  Widget build(BuildContext context) {
    return Container(
      // bottom21 → height * 0.031
      margin: EdgeInsets.only(bottom: size.height * 0.031),
      // top9AndBottom4AndLeft13
      padding: EdgeInsets.only(
        top: size.height * 0.012,
        left: size.width * 0.033,
        bottom: size.height * 0.005,
      ),
      decoration: BoxDecoration(
        color: ColorsStyle.mediumWhiteColor,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Check-inside-circle icon placeholder
          const ShimmerCircle(diameter: 20),
          const Spacer(),
          // Right-side content column
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              // Subject name (TextMedium14)
              ShimmerBox(width: size.width * 0.38, height: 14, borderRadius: 7),
              SizedBox(height: size.height * 0.011), // height8
              // Exam content (TextMedium10)
              ShimmerBox(width: size.width * 0.27, height: 10, borderRadius: 5),
              SizedBox(height: size.height * 0.014), // height10
              // Course row: text + star-circle icon
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  ShimmerBox(
                    width: size.width * 0.22,
                    height: 10,
                    borderRadius: 5,
                  ),
                  SizedBox(width: size.width * 0.024), // width10
                  const ShimmerCircle(diameter: 18),
                ],
              ),
              SizedBox(height: size.height * 0.007), // height5
              // Classroom row: text + location-circle icon
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  ShimmerBox(
                    width: size.width * 0.2,
                    height: 10,
                    borderRadius: 5,
                  ),
                  SizedBox(width: size.width * 0.024), // width10
                  const ShimmerCircle(diameter: 18),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _TimeColumnSkeleton extends StatelessWidget {
  const _TimeColumnSkeleton({required this.size, required this.isFirst});
  final Size size;
  final bool isFirst;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        // First item gets height24, others get height10 (matches real widget)
        SizedBox(
          height: isFirst ? size.height * 0.033 : size.height * 0.014,
        ),
        // Start time placeholder (TextMedium12)
        ShimmerBox(width: size.width * 0.1, height: 12, borderRadius: 6),
        SizedBox(height: size.height * 0.023), // height16
        // End time placeholder (TextMedium12, grey)
        ShimmerBox(width: size.width * 0.1, height: 12, borderRadius: 6),
      ],
    );
  }
}
