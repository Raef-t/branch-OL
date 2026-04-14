import 'package:flutter/material.dart';
import '/core/components/text_medium10_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/home/presentation/view/widgets/custom_two_circle_avatars_in_home_view.dart';

class CustomTwoCircleAvatarsWithTextInHomeView extends StatelessWidget {
  const CustomTwoCircleAvatarsWithTextInHomeView({
    super.key,
    required this.number,
    required this.name,
    this.onTap,
  });
  final int number;
  final String name;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return GestureDetector(
      onTap: onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          CustomTwoCircleAvatarsInHomeView(number: number),
          Heights.height6(context: context),
          SizedBox(
            width: size.width * 0.14,
            child: TextMedium10Component(
              text: name,
              color: Colors.black,
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }
}
