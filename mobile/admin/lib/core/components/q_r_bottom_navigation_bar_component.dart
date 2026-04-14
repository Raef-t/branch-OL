import 'package:flutter/material.dart';
import 'package:flutter_liquid_glass_plus/flutter_liquid_glass.dart';
import 'package:second_page_app/core/paddings/padding_with_child/only_padding_with_child.dart';
import 'package:second_page_app/core/styles/colors_style.dart';

import '/core/components/contain_bottom_navigation_bar_card_to_many_view_component.dart';
import '/core/components/circle_q_r_in_bottom_navigation_bar_component.dart';

class QRBottomNavigationBarComponent extends StatelessWidget {
  const QRBottomNavigationBarComponent({
    super.key,
    required this.currentIndex,
    required this.onTap,
  });

  final int currentIndex;
  final void Function(int) onTap;

  @override
  Widget build(BuildContext context) {
    final width = MediaQuery.sizeOf(context).width;
    final bottomPadding = MediaQuery.paddingOf(context).bottom;
    final extraBottom = bottomPadding > 0 ? bottomPadding : 8.0;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      mainAxisAlignment: MainAxisAlignment.end,
      mainAxisSize: MainAxisSize.min,
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(36),
          child: LGContainer(
            padding: EdgeInsets.symmetric(
              horizontal: MediaQuery.sizeOf(context).width * 0.05,
            ),
            height: 74,
            width: width,
            useOwnLayer: true,
            settings: const LiquidGlassSettings(blur: 18.0, thickness: 0.8),

            child: Container(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(36),
                border: Border(
                  left: BorderSide(
                    color: ColorsStyle.littleVinicColor.withValues(
                      alpha: 0.3,
                    ),
                    width: 1,
                  ),
                  right: BorderSide(
                    color: ColorsStyle.littleVinicColor.withValues(
                      alpha: 0.3,
                    ),
                    width: 1,
                  ),
                  bottom: BorderSide(
                    color: ColorsStyle.littleVinicColor.withValues(
                      alpha: 0.3,
                    ),
                    width: 3,
                  ),
                ),
                color: Colors.white.withValues(alpha: 0.12),
              ),
              child: ContainBottomNavigationBarCardToManyViewComponent(
                currentIndex: currentIndex,
                onTap: onTap,
              ),
            ),
          ),
        ),
        SizedBox(height: extraBottom),
      ],
    );
  }
}
