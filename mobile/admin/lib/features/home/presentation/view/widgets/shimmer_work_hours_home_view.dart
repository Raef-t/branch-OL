import 'package:flutter/material.dart';
import '/core/components/shimmer_loading_widget.dart';

/// Skeleton loader for the work-hours/class-schedule section.
/// Shows 2 placeholder cards that mirror [CustomDetailsCardHomeView]:
///   left colored vertical bar | subject details column | time column
///   (Facebook post-card skeleton style).
class ShimmerWorkHoursHomeView extends StatelessWidget {
  const ShimmerWorkHoursHomeView({super.key});

  // Two accent colours matching the actual card list colours.
  static const List<Color> _accentColors = [
    Color(0xffDEFFE0), // veryLittleGreenColor
    Color(0xffD2F2FB), // veryLittleSkyBlueColor
  ];

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    return ShimmerLoadingWidget(
      child: Column(
        children: List.generate(2, (cardIndex) {
          return _WorkHoursSkeletonCard(
            size: size,
            accentColor: _accentColors[cardIndex],
          );
        }),
      ),
    );
  }
}

class _WorkHoursSkeletonCard extends StatelessWidget {
  const _WorkHoursSkeletonCard({
    required this.size,
    required this.accentColor,
  });
  final Size size;
  final Color accentColor;

  @override
  Widget build(BuildContext context) {
    return Container(
      // Mirror CustomDetailsCardHomeView margins/padding
      margin: EdgeInsets.only(
        left: size.width * 0.048,   // left20
        bottom: size.height * 0.031, // bottom23
      ),
      padding: EdgeInsets.only(left: size.width * 0.029), // left12
      color: const Color(0xffFBFBFB), // ColorsStyle.mediumWhiteColor
      child: IntrinsicHeight(
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // ── Left side (flex 3) ──────────────────────────────────────
            Expanded(
              flex: 3,
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  // Navigation arrows (two small circle placeholders)
                  const ShimmerCircle(diameter: 22),
                  SizedBox(width: size.width * 0.007),
                  const ShimmerCircle(diameter: 22),
                  // Subject details column
                  Expanded(
                    child: Padding(
                      padding: EdgeInsets.symmetric(
                        vertical: size.height * 0.011,
                        horizontal: size.width * 0.02,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          // Subject name
                          ShimmerBox(
                            width: size.width * 0.35,
                            height: 13,
                            borderRadius: 6,
                          ),
                          SizedBox(height: size.height * 0.008),
                          // Course / category
                          ShimmerBox(
                            width: size.width * 0.26,
                            height: 10,
                            borderRadius: 5,
                          ),
                          SizedBox(height: size.height * 0.006),
                          // Classroom / location
                          ShimmerBox(
                            width: size.width * 0.22,
                            height: 10,
                            borderRadius: 5,
                          ),
                          SizedBox(height: size.height * 0.006),
                          // Teacher row: avatar + name
                          Row(
                            mainAxisAlignment: MainAxisAlignment.end,
                            children: [
                              ShimmerBox(
                                width: size.width * 0.18,
                                height: 10,
                                borderRadius: 5,
                              ),
                              SizedBox(width: size.width * 0.015),
                              const ShimmerCircle(diameter: 22),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  SizedBox(width: size.width * 0.022), // width9
                  // Coloured vertical left-bar (matches VerticalLineThatClipper)
                  Container(
                    width: 5,
                    decoration: BoxDecoration(
                      color: accentColor,
                      borderRadius: const BorderRadius.only(
                        topLeft: Radius.circular(55),
                        bottomLeft: Radius.circular(55),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            SizedBox(width: size.width * 0.024), // width10
            // ── Right side (flex 1) — time column ───────────────────────
            Expanded(
              child: Padding(
                padding: EdgeInsets.symmetric(vertical: size.height * 0.011),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    // Start time + arrow icon
                    Row(
                      children: [
                        ShimmerBox(
                          width: size.width * 0.13,
                          height: 12,
                          borderRadius: 6,
                        ),
                        const Spacer(),
                        const ShimmerCircle(diameter: 16),
                      ],
                    ),
                    SizedBox(height: size.height * 0.008),
                    // End time
                    ShimmerBox(
                      width: size.width * 0.13,
                      height: 12,
                      borderRadius: 6,
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
